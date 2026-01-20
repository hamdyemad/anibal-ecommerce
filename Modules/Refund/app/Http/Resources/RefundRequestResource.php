<?php

namespace Modules\Refund\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Customer\app\Transformers\CustomerApiResource;
use Modules\Vendor\app\Http\Resources\Api\VendorApiResource;

class RefundRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'refund_number' => $this->refund_number,
            'order_number' => $this->order->order_number,
            'vendor' => $this->whenLoaded('vendor', new VendorApiResource($this->vendor)),
            'label' => trans('refund::refund.statuses.' . $this->status),
            // Amounts - Ordered logically (components first, then total at the end)
            'total_products_amount' => (float) $this->total_products_amount,
            'total_shipping_amount' => (float) $this->total_shipping_amount,
            'total_tax_amount' => (float) $this->total_tax_amount,
            'total_discount_amount' => (float) $this->total_discount_amount,
            'vendor_fees_amount' => (float) $this->vendor_fees_amount,
            'vendor_discounts_amount' => (float) $this->vendor_discounts_amount,
            'promo_code_amount' => (float) $this->promo_code_amount,
            'return_shipping_cost' => (float) $this->return_shipping_cost,
            'points_used' => (float) $this->points_used,
            'points_to_deduct' => $this->points_to_deduct,
            'total_refund_amount' => (float) $this->total_refund_amount, // Total at the end
            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Relationships
            'items' => RefundRequestItemResource::collection($this->whenLoaded('items')),
            'history' => RefundRequestHistoryResource::collection($this->whenLoaded('history')),
        ];
    }
}
