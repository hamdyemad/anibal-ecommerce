<?php

namespace Modules\Order\app\Repositories\Api;

use Modules\Order\app\Interfaces\Api\ShippingCalculationRepositoryInterface;
use Modules\Customer\app\Models\CustomerAddress;
use Modules\AreaSettings\app\Models\City;
use Modules\Order\app\Models\Shipping;
use App\Exceptions\OrderException;
use Illuminate\Support\Facades\Log;

class ShippingCalculationRepository implements ShippingCalculationRepositoryInterface
{
    /**
     * Calculate shipping cost for cart items based on customer address or city
     * Groups items by type (department/category/subcategory) and city, adds shipping cost once per group
     * Supports both existing customers (with address) and external customers (with city_id)
     * Returns both total shipping and per-product shipping breakdown
     * 
     * Cart items format: [
     *   'type' => 'department|category|subcategory',
     *   'type_id' => int,
     *   'type_name' => string,
     *   'product_id' => int (vendor_product_id),
     *   'vendor_id' => int,
     *   'quantity' => int
     * ]
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

        // Get type from first cart item (all items should have same type)
        $groupByType = $cartItems[0]['type'] ?? 'category';

        Log::info('Shipping calculation debug', [
            'groupByType' => $groupByType,
            'cartItems' => $cartItems,
            'targetCityId' => $targetCityId,
        ]);

        // Group cart items by type_id
        $itemsByType = $this->groupItemsByTypeId($cartItems);

        Log::info('Grouped items', ['itemsByType' => $itemsByType]);

        // Calculate shipping for each type-city combination
        $shippingBreakdown = [];
        $totalShippingCost = 0;
        $productShipping = []; // Per-product shipping costs

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
                $shippingCost = (float) $shipping->cost;
                $itemsCount = count($items);
                
                // Distribute shipping cost equally among products in this group
                $shippingPerProduct = $itemsCount > 0 ? $shippingCost / $itemsCount : 0;
                
                // Assign shipping cost to each product in this group
                foreach ($items as $item) {
                    $productId = $item['product_id'] ?? null;
                    $vendorId = $item['vendor_id'] ?? null;
                    
                    if ($productId) {
                        // Use vendor_product_id as key for per-product shipping
                        $productShipping[$productId] = [
                            'vendor_product_id' => $productId,
                            'vendor_id' => $vendorId,
                            'shipping_cost' => round($shippingPerProduct, 2),
                            'type' => $groupByType,
                            'type_id' => $typeId,
                        ];
                    }
                }
                
                $shippingBreakdown[] = [
                    'type' => $groupByType,
                    'type_id' => $typeId,
                    'type_name' => $items[0]['type_name'] ?? null,
                    'city_id' => $targetCityId,
                    'city_name' => $cityName,
                    'shipping_id' => $shipping->id,
                    'shipping_name' => $shipping->getTranslation('name', app()->getLocale()),
                    'cost' => $shippingCost,
                    'items_count' => $itemsCount,
                    'cost_per_product' => round($shippingPerProduct, 2),
                ];
                $totalShippingCost += $shippingCost;
            }
        }

        return [
            'shipping_cost' => (float) $totalShippingCost,
            'breakdown' => $shippingBreakdown,
            'product_shipping' => $productShipping,
            'address' => $addressInfo
        ];
    }

    /**
     * Find shipping for a specific type (department/category/subcategory) and city
     */
    private function findShippingForTypeAndCity($typeId, $cityId, $type)
    {
        // Query shipping_categories pivot table directly for more reliable results
        $query = Shipping::withoutGlobalScope('country_filter')
            ->where('active', 1)
            ->whereHas('cities', function($q) use ($cityId) {
                $q->withoutGlobalScope('country_filter')
                  ->where('cities.id', $cityId);
            })
            // Join shipping_categories directly to filter by type and type_id
            ->whereExists(function ($subQuery) use ($typeId, $type) {
                $subQuery->select(\DB::raw(1))
                    ->from('shipping_categories')
                    ->whereColumn('shipping_categories.shipping_id', 'shippings.id')
                    ->where('shipping_categories.type', $type)
                    ->where('shipping_categories.type_id', $typeId);
            });

        Log::info('Shipping query SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        return $query->first();
    }

    /**
     * Group cart items by type_id
     */
    private function groupItemsByTypeId(array $cartItems): array
    {
        $grouped = [];

        foreach ($cartItems as $item) {
            $typeId = $item['type_id'] ?? null;

            if (!$typeId) {
                continue;
            }

            if (!isset($grouped[$typeId])) {
                $grouped[$typeId] = [];
            }

            $grouped[$typeId][] = $item;
        }

        return $grouped;
    }
}
