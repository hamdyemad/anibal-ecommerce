<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

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
        $variant = $this->vendorProductVariant;
        $vendorProduct = $variant?->vendorProduct;
        $product = $vendorProduct?->product;
        $locale = app()->getLocale();

        if (!$vendorProduct || !$product) {
            return [];
        }

        // Get reviews data
        $totalReviews = $vendorProduct->reviews_count ?? $vendorProduct->reviews()->count();
        $avgStar = $vendorProduct->reviews_avg_star ?? $vendorProduct->reviews()->avg('star') ?? 0;

        // Build variants array with the product nested inside
        $variants = [];
        if ($variant) {
            $variantData = $this->buildVariantData($variant, $vendorProduct, $product, $locale);
            $variants[] = $variantData;
        }

        return [
            'id' => $vendorProduct->id,
            'show_end_offer_at_section' => (bool) ($variant?->has_discount ?? false),
            'image' => formatImage($product->mainImage),
            'name' => $product->getTranslation('title', $locale) ?? $product->title,
            'slug' => $product->slug,
            'points' => $vendorProduct->points ?? 0,
            'sku' => $vendorProduct->sku ?? $variant?->sku,
            'details' => $product->getTranslation('details', $locale) ?? '',
            'summary' => $product->getTranslation('summary', $locale) ?? '',
            'instructions' => $product->getTranslation('instructions', $locale) ?? '',
            'features' => $product->getTranslation('features', $locale) ?? '',
            'extras' => $product->getTranslation('extra_description', $locale) ?? '',
            'star' => round($avgStar, 1),
            'num_of_user_review' => $totalReviews,
            'number_of_sale' => $product->sales ?? 0,
            'video_link' => $product->video_link ?? '',
            'stock' => $vendorProduct->total_stock ?? 0,
            'views' => $product->views ?? 0,
            'matrial' => $product->getTranslation('material', $locale) ?? '',
            'status' => $vendorProduct->is_active ? 'Active' : 'Inactive',
            'limitation' => $vendorProduct->max_per_order ?? 10,
            'is_fav' => $vendorProduct->is_fav ?? false,
            'size_color_type' => $this->getSizeColorType($product),
            'variants' => $variants,
            'tags' => $product->tags_array ?? [],
            'meta_description' => $product->getTranslation('meta_description', $locale) ?? '',
            'meta_keywords' => $product->meta_keywords ?? [],
        ];
    }

    /**
     * Build variant data with nested product
     */
    private function buildVariantData($variant, $vendorProduct, $product, $locale): array
    {
        // Build configuration
        $configuration = null;
        if ($variant->variantConfiguration) {
            $colorValue = null;
            if ($variant->variantConfiguration->type === 'color') {
                $colorValue = $variant->variantConfiguration->value;
            }
            
            $configuration = [
                'id' => $variant->variantConfiguration->id,
                'name' => $variant->variantConfiguration->getTranslation('name', $locale) ?? $variant->variantConfiguration->value,
                'color' => $colorValue,
                'key' => $variant->variantConfiguration->key ? [
                    'id' => $variant->variantConfiguration->key->id,
                    'name' => $variant->variantConfiguration->key->getTranslation('name', $locale) ?? $variant->variantConfiguration->key->name,
                ] : null,
            ];
        }

        // Calculate countdown
        $countDown = null;
        if ($variant->discount_end_date) {
            $endDate = \Carbon\Carbon::parse($variant->discount_end_date);
            $now = now();
            $diff = $now->diff($endDate);
            
            $countDown = [
                'product_id' => $vendorProduct->id,
                'days' => $diff->invert ? 0 : $diff->days,
                'hours' => $diff->invert ? 0 : $diff->h,
                'minutes' => $diff->invert ? 0 : $diff->i,
                'seconds' => $diff->invert ? 0 : $diff->s,
            ];
        }

        // Get reviews data for nested product
        $totalReviews = $vendorProduct->reviews_count ?? $vendorProduct->reviews()->count();
        $avgStar = $vendorProduct->reviews_avg_star ?? $vendorProduct->reviews()->avg('star') ?? 0;

        return [
            'id' => $variant->id,
            'show_end_offer_at_section' => (bool) $variant->has_discount,
            'stock' => $variant->total_stock ?? 0,
            'sku' => $variant->sku,
            'variant_name' => $variant->{"variant_path_{$locale}"} ?? '',
            'configuration' => $configuration,
            'real_price' => round(($this->special_price ?? $variant->price), 2),
            'fake_price' => $variant->price_before_discount ? round($variant->price_before_discount, 2) : null,
            'discount' => $variant->discount,
            'quantity_in_cart' => $variant->quantity_in_cart ?? null,
            'cart_id' => $variant->cart_id ?? null,
            'countDeliveredProduct' => $variant->fulfilled_stock ?? 0,
            'countOfAvailable' => $variant->remaining_stock ?? $variant->total_stock ?? 0,
            'end_at' => $variant->discount_end_date ? \Carbon\Carbon::parse($variant->discount_end_date)->format('Y-m-d') : null,
            'countDown' => $countDown,
            'product' => [
                'id' => $vendorProduct->id,
                'image' => formatImage($product->mainImage),
                'name' => $product->getTranslation('title', $locale) ?? $product->title,
                'slug' => $product->slug,
                'points' => $vendorProduct->points ?? 0,
                'sku' => $vendorProduct->sku ?? $variant->sku,
                'details' => $product->getTranslation('details', $locale),
                'summary' => $product->getTranslation('summary', $locale),
                'instructions' => $product->getTranslation('instructions', $locale),
                'features' => $product->getTranslation('features', $locale),
                'extras' => $product->getTranslation('extra_description', $locale),
                'star' => round($avgStar, 1),
                'num_of_user_review' => $totalReviews,
                'number_of_sale' => $product->sales ?? 0,
                'video_link' => $product->video_link,
                'stock' => $vendorProduct->total_stock ?? 0,
                'views' => $product->views ?? 0,
                'matrial' => $product->getTranslation('material', $locale),
                'shipping' => null,
                'status' => $vendorProduct->is_active ? 'Active' : 'Inactive',
                'limitation' => $vendorProduct->max_per_order ?? 10,
                'is_fav' => $vendorProduct->is_fav ?? false,
                'size_color_type' => $this->getSizeColorType($product),
            ],
        ];
    }

    /**
     * Get size/color type based on configuration
     */
    private function getSizeColorType($product): string
    {
        if ($product->configuration_type === 'simple') {
            return 'without_any';
        }
        return 'with_variants';
    }
}
