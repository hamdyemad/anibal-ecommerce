<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightCategoryApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightDepartmentApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightSubCategoryApiResource;
use Modules\Vendor\app\Http\Resources\Api\LightVendorResource;

class ProductBySlugResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this['product'];
        $vendorProducts = $this['vendorProducts'];
        $reviews = $this['reviews'];
        
        // Get vendor slug from request params
        $vendorSlug = $request->query('vendor_slug') ?? $request->query('vendor');
        
        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'vendors' => $vendorProducts->map(function($vendorProduct) use($vendorSlug, $product, $reviews) {
                $isSelected = $vendorSlug && $vendorProduct->vendor && $vendorProduct->vendor->slug === $vendorSlug;
                
                // Get the vendor product resource data
                $vendorProductData = (new VendorProductResource($vendorProduct))->resolve();
                
                // Add all product-specific data
                $vendorProductData['name'] = $product->name;
                $vendorProductData['description'] = $product->description;
                $vendorProductData['details'] = $product->details;
                $vendorProductData['summary'] = $product->summary;
                $vendorProductData['instructions'] = $product->instructions;
                $vendorProductData['features'] = $product->features;
                $vendorProductData['extras'] = $product->extra_description;
                $vendorProductData['material'] = $product->material;
                $vendorProductData['video_link'] = $product->video_link;
                $vendorProductData['tags'] = $product->tags_array ?? [];
                $vendorProductData['meta_description'] = $product->meta_description;
                $vendorProductData['meta_keywords'] = $product->meta_keywords ?? [];
                $vendorProductData['image'] = formatImage($product->mainImage);
                $vendorProductData['images'] = $product->additionalImages?->map(fn($img) => formatImage($img))->filter()->values() ?? [];
                
                // Add brand, category, department, sub_category, reviews
                $vendorProductData['brand'] = $product->brand ? [
                    'id' => $product->brand->id,
                    'title' => $product->brand->name,
                    'slug' => $product->brand->slug,
                    'image' => formatImage($product->brand->logo),
                    'cover' => formatImage($product->brand->cover),
                    'type' => 'brand',
                    'icon' => formatImage($product->brand->logo),
                ] : null;
                $vendorProductData['category'] = (new LightCategoryApiResource($product->category))->resolve();
                $vendorProductData['department'] = (new LightDepartmentApiResource($product->department))->resolve();
                $vendorProductData['sub_category'] = $product->subCategory ? (new LightSubCategoryApiResource($product->subCategory))->resolve() : null;
                $vendorProductData['reviews'] = $reviews;
                
                return [
                    'vendor' => new LightVendorResource($vendorProduct->vendor),
                    'selected' => $isSelected,
                    'vendor_product' => $vendorProductData,
                ];
            })->values()
        ];
    }
}
