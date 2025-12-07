<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->vendorProduct->product->id,
            'image' => formatImage($this->vendorProduct->product->mainImage),
            'name' => $this->vendorProduct->product->title,
            'slug' => $this->vendorProduct->product->slug,
            'points' => $this->vendorProduct->points ?? 0,
            'status' => $this->vendorProduct->is_featured ? __('catalogmanagement::product.featured') : __('catalogmanagement::product.active'),
            'is_fav' => false,
            'star' => $this->vendorProduct->average_rating ?? 0,
            'num_of_user_review' => $this->vendorProduct->reviews_count ?? 0,
            'number_of_sale' => $this->vendorProduct->sales ?? 0,
            'stock' => $this->total_stock,
            'sku' => $this->sku ?? null,
            'variant_id' => $this->id,
            'variant_name' => $this->{"variant_path_{$locale}"} ?? '',
            'real_price' => number_format((float) $this->price, 2),
            'fake_price' => $this->price_before_discount ? number_format((float) $this->price_before_discount, 2) : null,
            'discount' => $this->discount,
            'countDeliveredProduct' => $this->countDeliveredProduct,
            'countOfAvailable' => $this->countOfAvailable,
        ];
    }
}
