<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Http\Resources\Api\ProductListResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Uses ProductListResource to maintain consistency with product listing
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Check if vendor product exists and is loaded
        if (!$this->relationLoaded('vendorProduct') || !$this->vendorProduct) {
            return [];
        }

        // Use ProductListResource to format the vendor product
        $productResource = new ProductListResource($this->vendorProduct);
        $productData = $productResource->toArray($request);
        
        // Override is_fav to always be true in wishlist
        $productData['is_fav'] = true;
        
        // Add wishlist-specific fields
        $productData['wishlist_id'] = $this->id;
        
        return $productData;
    }
}
