<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Vendor\app\Http\Resources\Api\VendorApiResource;

class VendorProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'points' => $this->points,
            'max_per_order' => $this->max_per_order,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,

            // Product details
            'product' => new ProductResource($this->whenLoaded('product')),

            // Vendor details
            'vendor' => new VendorApiResource($this->whenLoaded('vendor')),

            // Tax details
            'tax' => new TaxResource($this->whenLoaded('tax')),

            // Variants with pricing
            'variants' => VendorProductVariantResource::collection($this->whenLoaded('variants')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
