<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku ?? $this->vendorProduct?->sku,
            'name' => $this->getTranslation('title', app()->getLocale()) ?? $this->name,
            'configuration_type' => $this->configuration_type,
            'brand_id' => $this->brand_id,
            'department_id' => $this->department_id,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'brand' => $this->brand ? $this->brand->getTranslation('name', app()->getLocale()) : '',
            'department' => $this->department ? $this->department->getTranslation('name', app()->getLocale()) : '',
            'category' => $this->category ? $this->category->getTranslation('name', app()->getLocale()) : '',
            'sub_category' => $this->subCategory ? $this->subCategory->getTranslation('name', app()->getLocale()) : '',
            'image' => $this->mainImage
                ? asset('storage/' . $this->mainImage->path)
                : asset('assets/img/default.png'),
            'gallery' => $this->additionalImages->map(function($img) {
                return asset('storage/' . $img->path);
            }),
            'translations' => $this->translations->groupBy('lang_id')->map(function($items) {
                return $items->pluck('lang_value', 'lang_key');
            }),
            'variants' => BankProductVariantResource::collection($this->variants),
        ];
    }
}
