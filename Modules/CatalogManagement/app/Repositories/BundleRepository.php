<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\BundleRepositoryInterface;
use Modules\CatalogManagement\app\Models\Bundle;
use App\Models\Language;
use App\Models\Attachment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BundleRepository implements BundleRepositoryInterface
{
    protected $bundle;

    public function __construct(Bundle $bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * Get all bundles with filters
     */
    public function getAllBundles($filters = [], $perPage = 15)
    {
        $query = Bundle::filter($filters)
        ->latest();
        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get bundle by ID
     */
    public function getBundleById($id)
    {
        return Bundle::
        where('id', $id)
        ->orwhere('slug', $id)->first();
    }

    /**
     * Create bundle
     */
    public function createBundle($data)
    {
        $bundle = Bundle::create([
            'country_id' => $data['country_id'],
            'vendor_id' => $data['vendor_id'],
            'bundle_category_id' => $data['bundle_category_id'],
            'sku' => $data['sku'],
            'slug' => \Str::uniqid(),
            'is_active' => $data['is_active'] ?? true,
        ]);

        // Handle main image upload
        $this->handleMainImage($bundle, $data, false);

        // Store translations
        $this->storeTranslations($bundle, $data);

        return $bundle;
    }

    /**
     * Update bundle
     */
    public function updateBundle($bundle, $data)
    {
        $bundle->update([
            'vendor_id' => $data['vendor_id'] ?? $bundle->vendor_id,
            'bundle_category_id' => $data['bundle_category_id'] ?? $bundle->bundle_category_id,
            'sku' => $data['sku'] ?? $bundle->sku,
            'is_active' => $data['is_active'] ?? $bundle->is_active,
        ]);

        // Handle main image upload
        $this->handleMainImage($bundle, $data, true);

        // Update translations
        $this->storeTranslations($bundle, $data);

        return $bundle;
    }

    /**
     * Delete bundle
     */
    public function deleteBundle($bundle)
    {
        $bundle->delete();
        return true;
    }

    /**
     * Store translations
     */
    public function storeTranslations($bundle, $data)
    {
        if (!isset($data['translations'])) {
            return;
        }

        $languages = Language::all()->keyBy('code');
        $translationFields = ['name', 'description', 'seo_title', 'seo_description', 'seo_keywords'];

        foreach ($data['translations'] as $langId => $translationData) {
            $language = $languages->firstWhere('id', $langId);
            if (!$language) {
                continue;
            }

            foreach ($translationFields as $field) {
                if (isset($translationData[$field])) {
                    $bundle->translations()->updateOrCreate(
                        [
                            'lang_id' => $language->id,
                            'lang_key' => $field,
                        ],
                        [
                            'lang_value' => $translationData[$field],
                        ]
                    );
                }
            }
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($bundle)
    {
        $bundle->update(['is_active' => !$bundle->is_active]);
        return $bundle;
    }

    /**
     * Handle main image upload
     */
    protected function handleMainImage(Bundle $bundle, array $data, bool $isUpdate = false): void
    {
        if (isset($data['image'])) {
            // Delete old main image if updating
            if ($isUpdate) {
                $oldMainImage = $bundle->attachments()
                    ->where('type', 'main_image')
                    ->first();
                if ($oldMainImage) {
                    Storage::disk('public')->delete($oldMainImage->path);
                    $oldMainImage->delete();
                }
            }

            // Store new main image
            $mainImagePath = $data['image']->store('bundles/images', 'public');
            Attachment::create([
                'attachable_type' => Bundle::class,
                'attachable_id' => $bundle->id,
                'type' => 'main_image',
                'path' => $mainImagePath,
            ]);
        }
    }
}
