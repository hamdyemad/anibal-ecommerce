<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'vendor_id' => $this->vendor_id,
            'bundle_category_id' => $this->bundle_category_id,
            'is_active' => $this->is_active,
            'country_id' => $this->country_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Translations
            'name' => $this->getTranslation('name'),
            'description' => $this->getTranslation('description'),
            'seo_title' => $this->getTranslation('seo_title'),
            'seo_description' => $this->getTranslation('seo_description'),
            'seo_keywords' => $this->getTranslation('seo_keywords'),

            // Relationships
            'vendor' => $this->vendor ? [
                'id' => $this->vendor->id,
                'name' => $this->vendor->name
            ] : null,

            'bundle_category' => $this->bundleCategory ? [
                'id' => $this->bundleCategory->id,
                'name' => $this->bundleCategory->name
            ] : null,

            'bundle_products' => BundleProductResource::collection($this->bundleProducts),
        ];
    }

    /**
     * Get all translations for a field
     */
    private function getTranslations($field)
    {
        $translations = [];
        foreach (['en', 'ar'] as $lang) {
            $translations[$lang] = $this->getTranslation($field, $lang);
        }
        return $translations;
    }
}
