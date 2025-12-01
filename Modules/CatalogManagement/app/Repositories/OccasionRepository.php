<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\OccasionRepositoryInterface;
use Modules\CatalogManagement\app\Models\Occasion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OccasionRepository implements OccasionRepositoryInterface
{
    /**
     * Get all occasions with optional filters
     */
    public function getOccasionsQuery(array $filters = [], $orderBy = null, $orderDirection = 'desc')
    {
        $query = Occasion::with(['translations', 'vendor'])->filter($filters);
        return $query;
    }

    /**
     * Get occasion by ID
     */
    public function getOccasionById($id)
    {
        return Occasion::with(['translations', 'vendor', 'occasionProducts'])->findOrFail($id);
    }

    /**
     * Create new occasion
     */
    public function createOccasion(array $data)
    {
        return DB::transaction(function () use ($data) {
            $occasion = Occasion::create([
                'vendor_id' => $data['vendor_id'],
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Store translations
            $this->storeTranslations($occasion, $data);

            // Store SEO data
            $this->storeSeo($occasion, $data);

            return $occasion;
        });
    }

    /**
     * Update occasion
     */
    public function updateOccasion($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $occasion = $this->getOccasionById($id);

            $occasion->update([
                'vendor_id' => $data['vendor_id'] ?? $occasion->vendor_id,
                'start_date' => $data['start_date'] ?? $occasion->start_date,
                'end_date' => $data['end_date'] ?? $occasion->end_date,
                'is_active' => $data['is_active'] ?? $occasion->is_active,
            ]);

            // Store translations (this will handle updates)
            $this->storeTranslations($occasion, $data);

            // Store SEO data
            $this->storeSeo($occasion, $data);

            return $occasion->fresh();
        });
    }

    /**
     * Delete occasion
     */
    public function deleteOccasion($id)
    {
        $occasion = $this->getOccasionById($id);
        return $occasion->delete();
    }

    /**
     * Get active occasions
     */
    public function getActiveOccasions()
    {
        return Occasion::active()->with(['translations', 'vendor'])->get();
    }

    /**
     * Toggle occasion status
     */
    public function toggleOccasionStatus($id)
    {
        $occasion = $this->getOccasionById($id);
        $occasion->update(['is_active' => !$occasion->is_active]);
        return $occasion->fresh();
    }

    /**
     * Store translations for occasion
     */
    protected function storeTranslations(Occasion $occasion, array $data): void
    {
        if (!isset($data['translations']) || !is_array($data['translations'])) {
            return;
        }

        // Get all languages
        $languages = \App\Models\Language::all();

        foreach ($languages as $language) {
            $translationData = $data['translations'][$language->id] ?? [];

            if (!empty($translationData['name'])) {
                // Use the Translation trait's storeTranslation method
                $occasion->storeTranslation([
                    'lang_id' => $language->id,
                    'lang_key' => 'name',
                    'lang_value' => $translationData['name'],
                ]);

                if (!empty($translationData['title'])) {
                    $occasion->storeTranslation([
                        'lang_id' => $language->id,
                        'lang_key' => 'title',
                        'lang_value' => $translationData['title'],
                    ]);
                }

                if (!empty($translationData['sub_title'])) {
                    $occasion->storeTranslation([
                        'lang_id' => $language->id,
                        'lang_key' => 'sub_title',
                        'lang_value' => $translationData['sub_title'],
                    ]);
                }
            }
        }
    }

    /**
     * Store SEO data for occasion
     */
    protected function storeSeo(Occasion $occasion, array $data): void
    {
        if (!isset($data['seo']) || !is_array($data['seo'])) {
            return;
        }

        // Get all languages
        $languages = \App\Models\Language::all();

        foreach ($languages as $language) {
            $seoData = $data['seo'][$language->id] ?? [];

            // Store SEO fields using Translation trait
            if (!empty($seoData['title'])) {
                $occasion->storeTranslation([
                    'lang_id' => $language->id,
                    'lang_key' => 'seo_title',
                    'lang_value' => $seoData['title'],
                ]);
            }

            if (!empty($seoData['description'])) {
                $occasion->storeTranslation([
                    'lang_id' => $language->id,
                    'lang_key' => 'seo_description',
                    'lang_value' => $seoData['description'],
                ]);
            }

            if (!empty($seoData['keywords'])) {
                $occasion->storeTranslation([
                    'lang_id' => $language->id,
                    'lang_key' => 'seo_keywords',
                    'lang_value' => $seoData['keywords'],
                ]);
            }
        }
    }
}
