<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Http\Resources\Api\TaxResource;
use Modules\CatalogManagement\app\Http\Resources\Api\OccasionResource;
use Modules\CatalogManagement\app\Http\Resources\Api\SimpleBundleResource;
use Modules\CatalogManagement\app\Models\BundleProduct;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $limit = $this->limitation();
        $prices = $this->calculatePricesWithLimitation();
        
        // Calculate total tax amount
        $totalTaxAmount = $prices['total_after_tax'] - $prices['total_before_tax'];
        
        return [
            'id' => $this->id,
            'product' => CartProductResource::make($this->vendorProductVariant),
            'type' => $this->type,
            'bundle' => ($this->bundle && $this->type === "bundle") ? new SimpleBundleResource($this->bundle) : null,
            'occasion' => ($this->occasion && $this->type === "occasion") ? new OccasionResource($this->occasion) : null,
            'limitation' => $limit[0],
            'min' => $limit[1],
            'taxes' => $this->vendorProduct && $this->vendorProduct->taxes ? $this->vendorProduct->taxes->map(function ($tax) use ($prices) {
                $amount = $prices['total_before_tax'] * ($tax->percentage / 100);
                return [
                    'id' => $tax->id,
                    'name' => $tax->name,
                    'percentage' => (float) $tax->percentage,
                    'amount' => round($amount, 2),
                ];
            }) : [],
            'quantity' => $this->quantity,
            'total_before_taxes' => round($prices['total_before_tax'], 2),
            'total_tax_amount' => round($totalTaxAmount, 2),
            'total_after_taxes' => round($prices['total_after_tax'], 2),
        ];
    }

    /**
     * Get limitation based on cart type
     */
    private function limitation()
    {
        if ($this->type === 'bundle') {
            $bundleProduct = BundleProduct::where(function ($query) {
                $query->where('bundle_id', $this->bundle_id)
                    ->where('vendor_product_variant_id', $this->vendor_product_variant_id);
            })->first();
            return [
                $bundleProduct?->limitation_quantity,
                $bundleProduct?->min_quantity
            ];
        }

        return [$this->vendorProductVariant?->max_per_order ?? 0, 0];
    }

    /**
     * Calculate prices considering bundle limitation
     * If quantity exceeds limitation, extra items use original variant price
     */
    private function calculatePricesWithLimitation()
    {
        $quantity = $this->quantity;
        $originalPriceBeforeTax = $this->vendorProductVariant?->price ?? 0;
        
        // Calculate tax rate
        $taxRate = $this->vendorProduct && $this->vendorProduct->taxes 
            ? $this->vendorProduct->taxes->sum('percentage') 
            : 0;
        $taxMultiplier = 1 + ($taxRate / 100);
        
        $originalPriceAfterTax = $originalPriceBeforeTax * $taxMultiplier;
        
        // Default: use original price
        $priceBeforeTax = $originalPriceBeforeTax;
        $priceAfterTax = $originalPriceAfterTax;
        $totalBeforeTax = $originalPriceBeforeTax * $quantity;
        $totalAfterTax = $originalPriceAfterTax * $quantity;
        
        if ($this->type === 'bundle' && $this->bundle_id) {
            $bundleProduct = BundleProduct::where('bundle_id', $this->bundle_id)
                ->where('vendor_product_variant_id', $this->vendor_product_variant_id)
                ->first();
            
            if ($bundleProduct) {
                $bundlePriceBeforeTax = (float) $bundleProduct->price;
                $bundlePriceAfterTax = $bundlePriceBeforeTax * $taxMultiplier;
                $limitQty = $bundleProduct->limitation_quantity ?? $quantity;
                
                // Use bundle price as the display price
                $priceBeforeTax = $bundlePriceBeforeTax;
                $priceAfterTax = $bundlePriceAfterTax;
                
                // Calculate quantities
                $bundleQty = min($quantity, $limitQty);
                $extraQty = max(0, $quantity - $limitQty);
                
                // Calculate totals
                $bundleTotalBeforeTax = $bundlePriceBeforeTax * $bundleQty;
                $extraTotalBeforeTax = $originalPriceBeforeTax * $extraQty;
                $totalBeforeTax = $bundleTotalBeforeTax + $extraTotalBeforeTax;
                
                $bundleTotalAfterTax = $bundlePriceAfterTax * $bundleQty;
                $extraTotalAfterTax = $originalPriceAfterTax * $extraQty;
                $totalAfterTax = $bundleTotalAfterTax + $extraTotalAfterTax;
            }
        } elseif ($this->type === 'occasion' && $this->occasion_id) {
            $occasionProduct = \Modules\CatalogManagement\app\Models\OccasionProduct::where('occasion_id', $this->occasion_id)
                ->where('vendor_product_variant_id', $this->vendor_product_variant_id)
                ->first();
            
            if ($occasionProduct) {
                $priceBeforeTax = (float) $occasionProduct->special_price;
                $priceAfterTax = $priceBeforeTax * $taxMultiplier;
                $totalBeforeTax = $priceBeforeTax * $quantity;
                $totalAfterTax = $priceAfterTax * $quantity;
            }
        }
        
        return [
            'price_before_tax' => $priceBeforeTax,
            'price_after_tax' => $priceAfterTax,
            'total_before_tax' => $totalBeforeTax,
            'total_after_tax' => $totalAfterTax,
        ];
    }
}
