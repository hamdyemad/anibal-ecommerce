<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorProductVariantStockResource extends JsonResource
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
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'region_id' => $this->region_id,
            'quantity' => $this->quantity,
            'region' => new RegionResource($this->whenLoaded('region')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
