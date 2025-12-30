<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OccasionProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $vendorProduct = $this->vendorProductVariant?->vendorProduct;
        $product = $vendorProduct?->product;
        $vendor = $vendorProduct?->vendor;
        $brand = $product?->brand;

        return [
            'id' => $this->id,
            'occasion_id' => $this->occasion_id,
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'special_price' => round($this->special_price, 2),
            'position' => $this->position,

            // Vendor Product Variant Information
            'vendor_product_variant' => [
                'id' => $this->vendorProductVariant?->id,
                'vendor_product_id' => $this->vendorProductVariant?->vendor_product_id,
                'slug' => $product?->slug,
                'sku' => $this->vendorProductVariant?->sku,
                'price' => $this->vendorProductVariant?->price,
                'variant_name' => $this->vendorProductVariant?->variantConfiguration?->name ?? 'Default',
                'product_name' => $product?->name ?? '-',
                'product_image' => formatImage($product?->mainImage),
                'star' => round($vendorProduct?->reviews_avg_star ?? $vendorProduct?->reviews?->avg('star') ?? 0, 1),
                'num_of_user_review' => $vendorProduct?->reviews_count ?? $vendorProduct?->reviews?->count() ?? 0,
            ],

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
