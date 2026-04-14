<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\PointsHelper;

class ProductListResource extends JsonResource
{
    /**
     * Lightweight resource for product listing pages
     * Returns only essential data needed for product cards
     */
    public function toArray(Request $request): array
    {
        // Use pre-calculated minimum price from query (no N+1 queries!)
        $priceBeforeTaxes = (float) ($this->min_variant_price ?? 0);
        
        // Calculate points based on minimum variant price
        $points = PointsHelper::calculatePoints($priceBeforeTaxes);
        
        // Calculate real price with taxes
        $taxes = $this->taxes ?? collect();
        $taxRate = $taxes->sum('percentage') ?? 0;
        $realPrice = $priceBeforeTaxes * (1 + ($taxRate / 100));
        
        // Calculate discount percentage
        $discount = null;
        $fakePrice = null;
        $fakePriceWithTax = null;
        
        if ($this->min_variant_has_discount && $this->min_variant_price_before_discount && $priceBeforeTaxes > 0) {
            $priceBeforeDiscount = (float) $this->min_variant_price_before_discount;
            if ($priceBeforeDiscount != 0) {
                $discount = round((($priceBeforeDiscount - $priceBeforeTaxes) / $priceBeforeDiscount) * 100, 2);
                $fakePriceWithTax = $priceBeforeDiscount * (1 + ($taxRate / 100));
            }
        }

        return [
            'id' => $this->id,
            'slug' => $this->product?->slug,
            'name' => $this->product?->title,
            'image' => formatImage($this->product?->mainImage),
            'points' => $points,
            'sku' => $this->sku,
            'status' => $this->is_featured ? __('catalogmanagement::product.featured') : __('catalogmanagement::product.active'),
            'is_fav' => $this->is_fav ?? false,
            'reviews_count' => $this->reviews_count ?? 0,
            'review_avg_star' => round($this->reviews_avg_star ?? 0, 1),
            'price_before_taxes' => number_format($priceBeforeTaxes, 2, '.', ''),
            'real_price' => number_format($realPrice, 2, '.', ''),
            'fake_price' => $fakePriceWithTax ? number_format($fakePriceWithTax, 2, '.', '') : null,
            'discount' => $discount,
            'remaining_stock' => $this->remaining_stock ?? 0,
            'vendor' => [
                'id' => $this->vendor_id,
                'name' => $this->vendor?->name,
                'slug' => $this->vendor?->slug,
            ],
            'brand' => $this->product?->brand ? [
                'id' => $this->product->brand->id,
                'title' => $this->product->brand->title,
                'slug' => $this->product->brand->slug,
            ] : null,
            'department' => $this->product?->department ? [
                'id' => $this->product->department->id,
                'name' => $this->product->department->name,
                'slug' => $this->product->department->slug,
            ] : null,
            'category' => $this->product?->category ? [
                'id' => $this->product->category->id,
                'name' => $this->product->category->name,
                'slug' => $this->product->category->slug,
            ] : null,
            'sub_category' => $this->product?->subCategory ? [
                'id' => $this->product->subCategory->id,
                'name' => $this->product->subCategory->name,
                'slug' => $this->product->subCategory->slug,
            ] : null,
        ];
    }
}
