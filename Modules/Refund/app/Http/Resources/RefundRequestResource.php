<?php

namespace Modules\Refund\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'order_id' => $this->order_id,
            'customer_id' => $this->customer_id,
            'vendor_id' => $this->vendor_id,
            'status' => $this->status,
            'status_label' => trans('refund::refund.statuses.' . $this->status),
            
            // Amounts
            'total_refund_amount' => (float) $this->total_refund_amount,
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
            
            // Notes
            'reason' => $this->reason,
            'customer_notes' => $this->customer_notes,
            'vendor_notes' => $this->vendor_notes,
            'admin_notes' => $this->admin_notes,
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'approved_at' => $this->approved_at?->toISOString(),
            'refunded_at' => $this->refunded_at?->toISOString(),
            
            // Relationships
            'items' => RefundRequestItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
