<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VendorProductResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VendorProductVariantResource;
use Modules\CatalogManagement\app\Models\VendorProduct;

class OccasionProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $variant = $this->vendorProductVariant;
        $vendor = $variant?->vendorProduct?->vendor;
        $brand = $variant?->vendorProduct?->product?->brand;

        // Load vendor product with all relations for VendorProductResource
        $vendorProduct = null;
        if ($variant) {
            $vendorProduct = VendorProduct::with([
                'product.mainImage', 
                'product.brand', 
                'product.department', 
                'product.category', 
                'product.subCategory', 
                'vendor', 
                'taxes',
                'variants'
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'star')
            ->find($variant->vendor_product_id);
        }

        return [
            'id' => $this->id,
            'occasion_id' => $this->occasion_id,
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'special_price' => round($this->special_price, 2),
            'position' => $this->position,

            // Vendor Product Variant Information
            'vendor_product_variant' => $variant ? new VendorProductVariantResource($variant) : null,

            // Full Vendor Product Resource
            'vendor_product' => $vendorProduct ? new VendorProductResource($vendorProduct) : null,

            // Vendor Information
            'vendor' => $vendor ? [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'slug' => $vendor->slug,
                'logo' => formatImage($vendor->logo),
                'star' => round($vendor->reviews_avg_star ?? $vendor->average_rating ?? 0, 1),
                'num_of_user_review' => $vendor->reviews_count ?? 0,
            ] : null,

            // Brand Information
            'brand' => $brand ? [
                'id' => $brand->id,
                'name' => $brand->getTranslation('name', app()->getLocale()) ?? $brand->name,
                'slug' => $brand->slug,
                'image' => formatImage($brand->attachments?->where('type', 'image')->first()),
            ] : null,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
