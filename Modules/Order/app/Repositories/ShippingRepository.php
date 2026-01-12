<?php

namespace Modules\Order\app\Repositories;

use Modules\Order\app\Interfaces\ShippingRepositoryInterface;
use Modules\Order\app\Models\Shipping;

class ShippingRepository implements ShippingRepositoryInterface
{
    /**
     * Get all shippings with filters
     */
    public function getAllShippings(array $filters)
    {
        $query = Shipping::with(['cities', 'categories', 'departments', 'subCategories', 'translations'])
            ->filter($filters)
            ->latest();

        // Pagination
        return $query->paginate(15);
    }

    /**
     * Get shipping by ID
     */
    public function getShippingById($id)
    {
        return Shipping::with(['cities', 'categories', 'departments', 'subCategories', 'country'])->findOrFail($id);
    }

    /**
     * Create a new shipping
     */
    public function createShipping(array $data)
    {
        // Get country ID from country code in URL
        $countryCode = request()->route('countryCode');
        $country = \Modules\AreaSettings\app\Models\Country::where('code', strtoupper($countryCode))->first();

        $shipping = Shipping::create([
            'cost' => $data['cost'],
            'active' => $data['active'] ?? 1,
            'country_id' => $country ? $country->id : null,
        ]);

        // Attach cities
        if (isset($data['city_ids']) && is_array($data['city_ids'])) {
            $shipping->cities()->attach($data['city_ids']);
        }

        // Attach categories
        if (isset($data['category_ids']) && is_array($data['category_ids'])) {
            $categoryData = [];
            foreach ($data['category_ids'] as $categoryId) {
                $categoryData[$categoryId] = ['type' => 'category'];
            }
            $shipping->categories()->attach($categoryData);
        }

        // Attach departments
        if (isset($data['department_ids']) && is_array($data['department_ids'])) {
            $departmentData = [];
            foreach ($data['department_ids'] as $departmentId) {
                $departmentData[$departmentId] = ['type' => 'department'];
            }
            $shipping->departments()->attach($departmentData);
        }

        // Attach sub categories
        if (isset($data['sub_category_ids']) && is_array($data['sub_category_ids'])) {
            $subCategoryData = [];
            foreach ($data['sub_category_ids'] as $subCategoryId) {
                $subCategoryData[$subCategoryId] = ['type' => 'subcategory'];
            }
            $shipping->subCategories()->attach($subCategoryData);
        }

        // Store translations
        if (isset($data['translations'])) {
            foreach ($data['translations'] as $langId => $translation) {
                if (!empty($translation['name'])) {
                    $shipping->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'name',
                        'lang_value' => $translation['name'],
                    ]);
                }
            }
        }

        return $shipping->load(['cities', 'categories', 'departments', 'subCategories']);
    }

    /**
     * Update shipping
     */
    public function updateShipping($id, array $data)
    {
        // Get country ID from country code in URL
        $countryCode = request()->route('countryCode');
        $country = \Modules\AreaSettings\app\Models\Country::where('code', strtoupper($countryCode))->first();

        $shipping = $this->getShippingById($id);
        $shipping->update([
            'cost' => $data['cost'],
            'active' => $data['active'] ?? 1,
            'country_id' => $country ? $country->id : null,
        ]);

        // Sync cities (removes old and adds new)
        if (isset($data['city_ids']) && is_array($data['city_ids'])) {
            $shipping->cities()->sync($data['city_ids']);
        }

        // Sync categories (removes old and adds new)
        if (isset($data['category_ids']) && is_array($data['category_ids'])) {
            $categoryData = [];
            foreach ($data['category_ids'] as $categoryId) {
                $categoryData[$categoryId] = ['type' => 'category'];
            }
            $shipping->categories()->sync($categoryData);
        } else {
            $shipping->categories()->detach();
        }

        // Sync departments (removes old and adds new)
        if (isset($data['department_ids']) && is_array($data['department_ids'])) {
            $departmentData = [];
            foreach ($data['department_ids'] as $departmentId) {
                $departmentData[$departmentId] = ['type' => 'department'];
            }
            $shipping->departments()->sync($departmentData);
        } else {
            $shipping->departments()->detach();
        }

        // Sync sub categories (removes old and adds new)
        if (isset($data['sub_category_ids']) && is_array($data['sub_category_ids'])) {
            $subCategoryData = [];
            foreach ($data['sub_category_ids'] as $subCategoryId) {
                $subCategoryData[$subCategoryId] = ['type' => 'subcategory'];
            }
            $shipping->subCategories()->sync($subCategoryData);
        } else {
            $shipping->subCategories()->detach();
        }

        // Update translations
        if (isset($data['translations'])) {
            foreach ($data['translations'] as $langId => $translation) {
                if (!empty($translation['name'])) {
                    // Update or create translation
                    $shipping->translations()->updateOrCreate(
                        [
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                        ],
                        [
                            'lang_value' => $translation['name'],
                        ]
                    );
                }
            }
        }
        return $shipping->load(['cities', 'categories', 'departments', 'subCategories']);
    }

    /**
     * Delete shipping
     */
    public function deleteShipping($id)
    {
        $shipping = $this->getShippingById($id);
        
        // Detach all relationships before deleting
        $shipping->cities()->detach();
        $shipping->categories()->detach();
        $shipping->departments()->detach();
        $shipping->subCategories()->detach();
        
        // Delete translations
        $shipping->translations()->delete();
        
        return $shipping->delete();
    }

    /**
     * Change shipping status
     */
    public function changeStatus($id, $active)
    {
        $shipping = $this->getShippingById($id);
        $shipping->update(['active' => $active]);
        return $shipping;
    }
}
