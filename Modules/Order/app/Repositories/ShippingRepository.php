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
        $query = Shipping::with(['city', 'category', 'translations'])->filter($filters);

        // Pagination
        return $query->paginate(15);
    }

    /**
     * Get shipping by ID
     */
    public function getShippingById($id)
    {
        return Shipping::findOrFail($id);
    }

    /**
     * Create a new shipping
     */
    public function createShipping(array $data)
    {
        $shipping = Shipping::create([
            'cost' => $data['cost'],
            'active' => $data['active'] ?? 1,
            'city_id' => $data['city_id'],
            'category_id' => $data['category_id'],
            'country_id' => $data['country_id'],
        ]);

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

        return $shipping;
    }

    /**
     * Update shipping
     */
    public function updateShipping($id, array $data)
    {
        $shipping = $this->getShippingById($id);
        $shipping->update([
            'cost' => $data['cost'],
            'active' => $data['active'] ?? 1,
            'city_id' => $data['city_id'],
            'category_id' => $data['category_id'],
            'country_id' => $data['country_id'],
        ]);

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
        return $shipping;
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
