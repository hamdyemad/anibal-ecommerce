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
        // Calculate points based on maximum variant price
        $price = $this->variants?->where('price', '>', 0)->max('price') ?? 0;
        $points = PointsHelper::calculatePoints((float) $price);

        // Get the first variant for simple products or max price variant
        $mainVariant = $this->variants?->where('price', '>', 0)->sortByDesc('price')->first();
        
        // Calculate real price with taxes
        $priceBeforeTaxes = (float) ($mainVariant?->price ?? 0);
        $taxes = $this->taxes ?? collect();
        $taxRate = $taxes->sum('percentage') ?? 0;
        $realPrice = $priceBeforeTaxes * (1 + ($taxRate / 100));
        
        // Get fake price (price before discount if discount is active)
        $fakePrice = null;
        if ($mainVariant && $mainVariant->has_discount && $mainVariant->price_before_discount) {
            $fakePrice = (float) $mainVariant->price_before_discount;
            $fakePriceWithTax = $fakePrice * (1 + ($taxRate / 100));
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
            'fake_price' => isset($fakePriceWithTax) ? number_format($fakePriceWithTax, 2, '.', '') : null,
            'discount' => $mainVariant?->discount,
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
        ];
    }
}
