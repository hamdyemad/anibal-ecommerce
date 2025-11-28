<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorProductVariantResource extends JsonResource
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
            'vendor_product_id' => $this->vendor_product_id,
            'variant_configuration_id' => $this->variant_configuration_id,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'has_discount' => $this->has_discount,
            'price_before_discount' => (float) $this->price_before_discount,
            'discount_end_date' => $this->discount_end_date,

            // Variant configuration details
            'variant_configuration' => new VariantConfigurationResource($this->whenLoaded('variantConfiguration')),

            // Stock information
            'stocks' => VendorProductVariantStockResource::collection($this->whenLoaded('stocks')),

            // Effective price (considering discount)
            'effective_price' => (float) $this->getEffectivePrice(),
            'is_discount_valid' => $this->isDiscountValid(),
            'total_stock' => $this->getTotalStock(),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
