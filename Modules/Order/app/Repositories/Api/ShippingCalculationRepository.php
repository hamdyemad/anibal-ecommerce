<?php

namespace Modules\Order\app\Repositories\Api;

use Modules\Order\app\Interfaces\Api\ShippingCalculationRepositoryInterface;
use Modules\Customer\app\Models\CustomerAddress;
use Modules\AreaSettings\app\Models\City;
use Modules\Order\app\Models\Shipping;
use Modules\SystemSetting\app\Models\SiteInformation;
use App\Exceptions\OrderException;
use Illuminate\Support\Facades\Log;

class ShippingCalculationRepository implements ShippingCalculationRepositoryInterface
{
    /**
     * Calculate shipping cost for cart items based on customer address or city
     * Groups items by department/category/subcategory (based on settings) and city, adds shipping cost once per group
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

        // Get shipping settings
        $shippingSettings = SiteInformation::first();
        
        // Determine which type to use for grouping based on settings
        // Priority: departments > subcategories > categories (default)
        $groupByType = 'category'; // default
        if ($shippingSettings?->shipping_allow_departments) {
            $groupByType = 'department';
        } elseif ($shippingSettings?->shipping_allow_sub_categories) {
            $groupByType = 'subcategory';
        } elseif ($shippingSettings?->shipping_allow_categories) {
            $groupByType = 'category';
        }

        Log::info('Shipping calculation debug', [
            'groupByType' => $groupByType,
            'settings' => [
                'allow_departments' => $shippingSettings?->shipping_allow_departments,
                'allow_categories' => $shippingSettings?->shipping_allow_categories,
                'allow_sub_categories' => $shippingSettings?->shipping_allow_sub_categories,
            ],
            'cartItems' => $cartItems,
            'targetCityId' => $targetCityId,
        ]);

        // Group cart items by the appropriate type
        $itemsByType = $this->groupItemsByType($cartItems, $groupByType);

        Log::info('Grouped items', ['itemsByType' => $itemsByType]);

        // Calculate shipping for each type-city combination
        $shippingBreakdown = [];
        $totalShippingCost = 0;

        foreach ($itemsByType as $typeId => $items) {
            // Get shipping for this type and city
            $shipping = $this->findShippingForTypeAndCity($typeId, $targetCityId, $groupByType);

            Log::info('Finding shipping for type', [
                'typeId' => $typeId,
                'cityId' => $targetCityId,
                'groupByType' => $groupByType,
                'shippingFound' => $shipping ? $shipping->id : null,
            ]);

            if ($shipping) {
                $shippingBreakdown[] = [
                    'type' => $groupByType,
                    'type_id' => $typeId,
                    'type_name' => $items[0]['type_name'] ?? null,
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
     * Find shipping for a specific type (department/category/subcategory) and city
     */
    private function findShippingForTypeAndCity($typeId, $cityId, $type)
    {
        $query = Shipping::withoutGlobalScope('country_filter')
            ->where('active', 1)
            ->whereHas('cities', function($q) use ($cityId) {
                $q->withoutGlobalScope('country_filter')
                  ->where('cities.id', $cityId);
            });

        // Add the appropriate relationship filter based on type
        // Use the shipping_categories pivot table with type filter
        switch ($type) {
            case 'department':
                $query->whereHas('departments', function($q) use ($typeId) {
                    $q->withoutGlobalScope('country_filter')
                      ->where('shipping_categories.type_id', $typeId);
                });
                break;
            case 'subcategory':
                $query->whereHas('subCategories', function($q) use ($typeId) {
                    $q->withoutGlobalScope('country_filter')
                      ->where('shipping_categories.type_id', $typeId);
                });
                break;
            case 'category':
            default:
                $query->whereHas('categories', function($q) use ($typeId) {
                    $q->withoutGlobalScope('country_filter')
                      ->where('shipping_categories.type_id', $typeId);
                });
                break;
        }

        Log::info('Shipping query SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        return $query->first();
    }

    /**
     * Group cart items by type (department/category/subcategory)
     */
    private function groupItemsByType(array $cartItems, string $type): array
    {
        $grouped = [];

        foreach ($cartItems as $item) {
            $typeId = null;
            $typeName = null;

            switch ($type) {
                case 'department':
                    $typeId = $item['department_id'] ?? null;
                    $typeName = $item['department_name'] ?? null;
                    break;
                case 'subcategory':
                    $typeId = $item['sub_category_id'] ?? null;
                    $typeName = $item['sub_category_name'] ?? null;
                    break;
                case 'category':
                default:
                    $typeId = $item['category_id'] ?? null;
                    $typeName = $item['category_name'] ?? null;
                    break;
            }

            if (!$typeId) {
                continue;
            }

            if (!isset($grouped[$typeId])) {
                $grouped[$typeId] = [];
            }

            $item['type_name'] = $typeName;
            $grouped[$typeId][] = $item;
        }

        return $grouped;
    }
}
