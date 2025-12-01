<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\BundleCategoryRepositoryInterface;
use Modules\CatalogManagement\app\Models\BundleCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BundleCategoryRepository implements BundleCategoryRepositoryInterface
{
    /**
     * Get all bundle categories with optional filters
     */
    public function getBundleCategoriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'desc')
    {
        $query = BundleCategory::with(['translations']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('lang_value', 'like', '%' . $filters['search'] . '%')
                  ->where('lang_key', 'name');
            });
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Apply sorting
        if ($orderBy) {
            if (is_array($orderBy) && isset($orderBy['lang_id'], $orderBy['key'])) {
                // Sort by translation
                $query->leftJoin('translations as t', function ($join) use ($orderBy) {
                    $join->on('bundle_categories.id', '=', 't.translatable_id')
                         ->where('t.translatable_type', '=', BundleCategory::class)
                         ->where('t.lang_id', '=', $orderBy['lang_id'])
                         ->where('t.lang_key', '=', $orderBy['key']);
                })
                ->orderBy('t.lang_value', $orderDirection)
                ->select('bundle_categories.*');
            } else {
                // Sort by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        } else {
            $query->orderBy('id', $orderDirection);
        }

        return $query;
    }

    /**
     * Get bundle category by ID
     */
    public function getBundleCategoryById($id)
    {
        return BundleCategory::with(['translations', 'attachments'])->findOrFail($id);
    }

    /**
     * Create new bundle category
     */
    public function createBundleCategory(array $data)
    {
        return DB::transaction(function () use ($data) {
            $bundleCategory = BundleCategory::create([
                'slug' => $data['slug'] ?? null,
                'active' => $data['active'] ?? 1,
            ]);

            // Store translations
            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    foreach ($translation as $key => $value) {
                        if (!empty($value)) {
                            $bundleCategory->translations()->create([
                                'lang_id' => $langId,
                                'lang_key' => $key,
                                'lang_value' => $value,
                            ]);
                        }
                    }
                }
            }

            return $bundleCategory;
        });
    }

    /**
     * Update bundle category
     */
    public function updateBundleCategory($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $bundleCategory = $this->getBundleCategoryById($id);

            $bundleCategory->update([
                'slug' => $data['slug'] ?? $bundleCategory->slug,
                'active' => $data['active'] ?? $bundleCategory->active,
            ]);

            // Update translations
            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    foreach ($translation as $key => $value) {
                        $bundleCategory->translations()->updateOrCreate(
                            [
                                'lang_id' => $langId,
                                'lang_key' => $key,
                            ],
                            [
                                'lang_value' => $value ?? '',
                            ]
                        );
                    }
                }
            }

            return $bundleCategory->fresh();
        });
    }

    /**
     * Delete bundle category
     */
    public function deleteBundleCategory($id)
    {
        $bundleCategory = $this->getBundleCategoryById($id);
        return $bundleCategory->delete();
    }

    /**
     * Get active bundle categories
     */
    public function getActiveBundleCategories()
    {
        return BundleCategory::active()->with(['translations'])->get();
    }

    /**
     * Toggle bundle category status
     */
    public function toggleBundleCategoryStatus($id)
    {
        $bundleCategory = $this->getBundleCategoryById($id);
        $bundleCategory->update(['active' => !$bundleCategory->active]);
        return $bundleCategory->fresh();
    }
}
