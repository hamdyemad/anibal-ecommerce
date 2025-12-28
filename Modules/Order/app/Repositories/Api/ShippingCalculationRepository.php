<?php

namespace Modules\Order\app\Repositories\Api;

use Modules\Order\app\Interfaces\Api\ShippingCalculationRepositoryInterface;
use Modules\Customer\app\Models\CustomerAddress;
use Modules\AreaSettings\app\Models\City;
use Modules\Order\app\Models\Shipping;
use App\Exceptions\OrderException;

class ShippingCalculationRepository implements ShippingCalculationRepositoryInterface
{
    /**
     * Calculate shipping cost for cart items based on customer address or city
     * Groups items by category and city, adds shipping cost once per group
     * Supports both existing customers (with address) and external customers (with city_id)
     */
    public function calculateShipping($customerId, $customerAddressId, array $cartItems, $cityId = null)
    {
        // Determine city_id from either customer address or direct city_id parameter
        $targetCityId = null;
        $cityName = null;
        $addressInfo = null;

        if ($customerAddressId && $customerId) {
            // Existing customer with address
            $address = CustomerAddress::withoutGlobalScope('country_filter')
                ->with('city')
                ->findOrFail($customerAddressId);

            if ($address->customer_id != $customerId) {
                throw new OrderException(trans('shipping.address_not_found'));
            }

            $targetCityId = $address->city_id;
            $cityName = $address->city->name ?? null;
            $addressInfo = [
                'id' => $address->id,
                'title' => $address->title,
                'city_id' => $address->city_id,
                'city_name' => $cityName,
            ];
        } elseif ($cityId) {
            // External customer with direct city_id
            $city = City::withoutGlobalScope('country_filter')->find($cityId);
            if (!$city) {
                throw new OrderException(trans('shipping.city_not_found'));
            }

            $targetCityId = $cityId;
            $cityName = $city->name ?? null;
            $addressInfo = [
                'id' => null,
                'title' => null,
                'city_id' => $cityId,
                'city_name' => $cityName,
            ];
        } else {
            throw new OrderException(trans('shipping.address_or_city_required'));
        }

        // Group cart items by category_id
        $itemsByCategory = $this->groupItemsByCategory($cartItems);

        // Calculate shipping for each category-city combination
        $shippingBreakdown = [];
        $totalShippingCost = 0;

        foreach ($itemsByCategory as $categoryId => $items) {
            // Get shipping for this category and city using many-to-many relationships
            // Bypass country filter to ensure we find the shipping
            $shipping = Shipping::withoutGlobalScope('country_filter')
                ->where('active', 1)
                ->whereHas('cities', function($q) use ($targetCityId) {
                    $q->withoutGlobalScope('country_filter')
                      ->where('cities.id', $targetCityId);
                })
                ->whereHas('categories', function($q) use ($categoryId) {
                    $q->withoutGlobalScope('country_filter')
                      ->where('categories.id', $categoryId);
                })
                ->first();

            if ($shipping) {
                $shippingBreakdown[] = [
                    'category_id' => $categoryId,
                    'category_name' => $items[0]['category_name'] ?? null,
                    'city_id' => $targetCityId,
                    'city_name' => $cityName,
                    'shipping_id' => $shipping->id,
                    'shipping_name' => $shipping->getTranslation('name', app()->getLocale()),
                    'cost' => (float) $shipping->cost,
                    'items_count' => count($items),
                ];
                $totalShippingCost += $shipping->cost;
            }
        }

        return [
            'shipping_cost' => (float) $totalShippingCost,
            'breakdown' => $shippingBreakdown,
            'address' => $addressInfo
        ];
    }

    /**
     * Group cart items by category
     */
    private function groupItemsByCategory(array $cartItems): array
    {
        $grouped = [];

        foreach ($cartItems as $item) {
            $categoryId = $item['category_id'] ?? null;

            if (!$categoryId) {
                continue;
            }

            if (!isset($grouped[$categoryId])) {
                $grouped[$categoryId] = [];
            }

            $grouped[$categoryId][] = $item;
        }

        return $grouped;
    }
}
