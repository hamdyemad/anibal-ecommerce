<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\app\Models\VendorOrderStage;
use Modules\Order\app\Traits\HasVariantConfigurationTree;

class OrderProductResource extends JsonResource
{
    use HasVariantConfigurationTree;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        
        // Calculate price before tax from stored taxes
        $price = (float) $this->price;
        $taxRate = $this->taxes ? $this->taxes->sum('percentage') : 0;
        $priceBeforeTax = $taxRate > 0 ? $price / (1 + $taxRate / 100) : $price;
        
        $unitPriceBeforeTax = round($priceBeforeTax / $this->quantity, 2);
        $unitPriceAfterTax = round($price / $this->quantity, 2);
        
        // Get vendor stage for this order product
        $vendorId = $this->vendorProduct?->vendor?->id ?? $this->vendor_id;
        $stage = $this->getVendorStage($vendorId);
        
        // Find delivered_at from vendor stage history
        $deliveredAt = $this->getVendorDeliveryDate($vendorId);
        
        // Build refund information
        $refundInfo = $this->buildRefundInfo($deliveredAt);
        
        return [
            'id' => $this->id,
            'vendor_product_id' => $this->vendor_product_id,
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'product' => [
                'id' => $this->vendorProduct?->product?->id,
                'name' => $this->vendorProduct?->product?->title,
                'slug' => $this->vendorProduct?->product?->slug,
                'refund_setting' => [
                    'is_able_to_refund' => $this->vendorProduct?->is_able_to_refund,
                    'refund_days' => get_refund_days($this->vendorProduct),
                ],
                'image' => formatImage($this->vendorProduct?->product?->mainImage),
            ],
            'vendor' => [
                'id' => $this->vendorProduct?->vendor?->id,
                'name' => $this->vendorProduct?->vendor?->getTranslation('name', $locale),
            ],
            'variant' => [
                'id' => $this->vendorProductVariant?->id,
                'sku' => $this->vendorProductVariant?->sku,
                'name' => $this->vendorProductVariant?->{"variant_path_{$locale}"},
            ],
            'stage' => $stage,
            'refund' => $refundInfo,
            'configuration_tree' => $this->when(
                $this->vendorProductVariant && 
                $this->vendorProductVariant->relationLoaded('variantConfiguration') && 
                $this->vendorProductVariant->variantConfiguration,
                function() use ($locale, $unitPriceBeforeTax, $unitPriceAfterTax) {
                    return $this->buildVariantConfigurationTree(
                        $this->vendorProductVariant->variantConfiguration, 
                        $this->vendorProductVariant->id,
                        $locale
                    );
                }
            ),
            'variant_price_before_taxes' => $unitPriceBeforeTax,
            'variant_real_price' => $unitPriceAfterTax,
            'unit_price_without_taxes' => $unitPriceBeforeTax,
            'taxes' => $this->taxes ? $this->taxes->map(function ($tax) {
                return [
                    'id' => $tax->id,
                    'tax_id' => $tax->tax_id,
                    'tax_name' => $tax->tax?->name,
                    'percentage' => (float) $tax->percentage,
                    'amount' => round((float) $tax->amount / $this->quantity, 2),
                ];
            }) : [],
            'unit_price_after_taxes' => $unitPriceAfterTax,
            'quantity' => $this->quantity,
            'total' => $price,
        ];
    }
    
    /**
     * Get vendor stage for this order product
     */
    private function getVendorStage(?int $vendorId): ?array
    {
        if (!$vendorId || !$this->order_id) {
            return null;
        }
        
        $vendorOrderStage = null;

        // Try to get from loaded relationship if available
        if ($this->relationLoaded('order') && $this->order && $this->order->relationLoaded('vendorStages')) {
            $vendorOrderStage = $this->order->vendorStages->firstWhere('vendor_id', $vendorId);
        }

        // Fallback for cases where relation is not loaded
        if (!$vendorOrderStage) {
            $vendorOrderStage = VendorOrderStage::where('order_id', $this->order_id)
                ->where('vendor_id', $vendorId)
                ->with(['stage' => function($q) {
                    $q->withoutGlobalScopes();
                }])
                ->first();
        }
        
        if (!$vendorOrderStage || !$vendorOrderStage->stage) {
            return null;
        }
        
        return [
            'id' => $vendorOrderStage->stage->id,
            'name' => $vendorOrderStage->stage->name,
            'color' => $vendorOrderStage->stage->color,
            'type' => $vendorOrderStage->stage->type,
        ];
    }

    /**
     * Get vendor delivery date from stage history
     */
    private function getVendorDeliveryDate(?int $vendorId): ?string
    {
        if (!$vendorId || !$this->order_id) {
            return null;
        }

        return \Modules\Refund\app\Helpers\RefundHelper::getVendorDeliveryDate($this->order_id, $vendorId);
    }

    /**
     * Build refund information for the product
     */
    private function buildRefundInfo(?string $deliveredAt): array
    {
        // Check if product allows refunds
        $isRefundable = $this->vendorProduct?->is_able_to_refund ?? false;
        
        if (!$isRefundable) {
            return [
                'is_refundable' => false,
                'is_eligible' => false,
                'reason' => trans('refund::refund.messages.product_not_refundable'),
                'refund_days' => null,
                'delivered_at' => null,
                'deadline' => null,
                'remaining_days' => null,
            ];
        }

        // Check if product was delivered
        if (!$deliveredAt) {
            return [
                'is_refundable' => true,
                'is_eligible' => false,
                'reason' => trans('refund::refund.messages.product_not_delivered'),
                'refund_days' => get_refund_days($this->vendorProduct),
                'delivered_at' => null,
                'deadline' => null,
                'remaining_days' => null,
            ];
        }

        // Check if product is eligible for refund (within refund window)
        $isEligible = is_eligible_for_refund($this->vendorProduct, $deliveredAt);
        $refundDays = get_refund_days($this->vendorProduct);
        $deadline = get_refund_deadline($this->vendorProduct, $deliveredAt);
        $remainingDays = get_remaining_refund_days($this->vendorProduct, $deliveredAt);

        // Check if already refunded
        $hasRefund = $this->refundItems()->exists();
        
        if ($hasRefund) {
            $refundRequest = $this->refundItems()->first()?->refundRequest;
            return [
                'is_refundable' => true,
                'is_eligible' => false,
                'reason' => trans('refund::refund.messages.product_already_refunded'),
                'refund_days' => $refundDays,
                'delivered_at' => $deliveredAt,
                'deadline' => $deadline?->toDateTimeString(),
                'remaining_days' => 0,
                'refund_request' => [
                    'id' => $refundRequest?->id,
                    'refund_number' => $refundRequest?->refund_number,
                    'status' => $refundRequest?->status,
                ],
            ];
        }

        return [
            'is_refundable' => true,
            'is_eligible' => $isEligible,
            'reason' => $isEligible ? null : trans('refund::refund.messages.refund_window_expired'),
            'refund_days' => $refundDays,
            'delivered_at' => $deliveredAt,
            'deadline' => $deadline?->toDateTimeString(),
            'remaining_days' => $remainingDays,
        ];
    }
}
