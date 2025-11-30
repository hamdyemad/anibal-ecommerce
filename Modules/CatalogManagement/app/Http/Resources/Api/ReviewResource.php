<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_product_id' => $this->vendor_product_id,
            'customer_id' => $this->customer_id,
            'customer' => [
                'id' => $this->customer?->id,
                'name' => $this->customer?->full_name,
                'email' => $this->customer?->email,
            ],
            'review' => $this->review,
            'star' => $this->star,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
