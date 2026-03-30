<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\GeneralResoruce;
use Modules\CategoryManagment\app\Http\Resources\Api\LightCategoryApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightDepartmentApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightSubCategoryApiResource;
use Modules\Vendor\app\Http\Resources\Api\LightVendorResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VendorProductVariantResource;
use App\Helpers\PointsHelper;

class VendorProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use pre-loaded counts from withCount/withAvg
        $totalReviews = $this->reviews_count ?? 0;
        $avgStar = $this->reviews_avg_star ?? 0;

        // Calculate points based on maximum variant price (excluding 0-priced variants)
        $price = $this->variants?->where('price', '>', 0)->max('price') ?? 0;
        $points = PointsHelper::calculatePoints((float) $price);

        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'product_id' => $this->product_id,
            'slug' => $this->product?->slug,
            'points' => $points,
            'sku' => $this->sku,
            'reviews_count' => $totalReviews,
            'review_avg_star' => round($avgStar, 2),
            'limitation' => $this->max_per_order,
            'status' => $this->is_featured ? __('catalogmanagement::product.featured') : __('catalogmanagement::product.active'),
            'number_of_sale' => $this->sales,
            'views' => $this->views,
            'sort_number' => $this->sort_number ?? 0,
            'stock' => $this->remaining_stock ?? 0,
            'is_fav' => $this->is_fav,
            'configuration_type' => $this->product?->configuration_type,
            'taxes' => TaxResource::collection($this->whenLoaded('taxes')),
            'configuration_tree' => $this->when($this->relationLoaded('variants'), function () {
                return $this->buildConfigurationTree();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Build configuration tree with all keys and their variants for this product
     */
    private function buildConfigurationTree(): array
    {
        $variants = $this->variants;

        if ($variants->isEmpty()) {
            return [];
        }

        $locale = app()->getLocale();
        $taxes = $this->taxes ?? collect();

        // Handle simple products
        if ($this->product?->configuration_type === 'simple') {
            $variant = $variants->first();
            return \App\Helpers\VariantTreeHelper::buildSimpleProductTree(
                $variant, 
                $this->product, 
                $taxes, 
                $locale
            );
        }

        // Handle variant products
        return \App\Helpers\VariantTreeHelper::buildConfigurationTree($variants, $taxes, $locale);
    }
}
