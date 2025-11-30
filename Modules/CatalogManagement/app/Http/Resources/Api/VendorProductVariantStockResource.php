<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\AreaSettings\app\Resources\RegionResource;

class VendorProductVariantStockResource extends JsonResource
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
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'region_id' => $this->region_id,
            'quantity' => $this->quantity,
            'region' => RegionResource::make($this->whenLoaded('region')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
