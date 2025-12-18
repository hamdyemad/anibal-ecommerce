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
        $query = Shipping::with(['cities', 'categories', 'translations'])->filter($filters);

        // Pagination
        return $query->paginate(15);
    }

    /**
     * Get shipping by ID
     */
    public function getShippingById($id)
    {
        return Shipping::with(['cities', 'categories', 'country'])->findOrFail($id);
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
            $shipping->categories()->attach($data['category_ids']);
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

        return $shipping->load(['cities', 'categories']);
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
            $shipping->categories()->sync($data['category_ids']);
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
        return $shipping->load(['cities', 'categories']);
    }

    /**
     * Delete shipping
     */
    public function deleteShipping($id)
    {
        $shipping = $this->getShippingById($id);
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
