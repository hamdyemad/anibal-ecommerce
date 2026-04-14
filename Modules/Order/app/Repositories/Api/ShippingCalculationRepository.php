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
     * Calculate shipping cost based on city only (simplified)
     * No category/department grouping - just one shipping cost per city
     * 
     * Cart items format: [
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

        Log::info('Shipping calculation - City based only', [
            'targetCityId' => $targetCityId,
            'cartItemsCount' => count($cartItems),
        ]);

        // Get shipping for this city (simple - no category/department filtering)
        $shipping = $this->findShippingForCity($targetCityId);

        if (!$shipping) {
            throw new OrderException(trans('shipping.not_available_for_city'));
        }

        $shippingCost = (float) $shipping->cost;
        $itemsCount = count($cartItems);
        
        // Distribute shipping cost equally among all products
        $shippingPerProduct = $itemsCount > 0 ? $shippingCost / $itemsCount : 0;
        
        // Assign shipping cost to each product
        $productShipping = [];
        foreach ($cartItems as $item) {
            $productId = $item['product_id'] ?? null;
            $vendorId = $item['vendor_id'] ?? null;
            
            if ($productId) {
                $productShipping[$productId] = [
                    'vendor_product_id' => $productId,
                    'vendor_id' => $vendorId,
                    'shipping_cost' => round($shippingPerProduct, 2),
                ];
            }
        }
        
        $shippingBreakdown = [
            [
                'city_id' => $targetCityId,
                'city_name' => $cityName,
                'shipping_id' => $shipping->id,
                'shipping_name' => $shipping->getTranslation('name', app()->getLocale()),
                'cost' => $shippingCost,
                'items_count' => $itemsCount,
                'cost_per_product' => round($shippingPerProduct, 2),
            ]
        ];

        return [
            'shipping_cost' => (float) $shippingCost,
            'breakdown' => $shippingBreakdown,
            'product_shipping' => $productShipping,
            'address' => $addressInfo
        ];
    }

    /**
     * Find shipping for a city (simplified - no category/department filtering)
     */
    private function findShippingForCity($cityId)
    {
        return Shipping::withoutGlobalScope('country_filter')
            ->where('active', 1)
            ->whereHas('cities', function($q) use ($cityId) {
                $q->withoutGlobalScope('country_filter')
                  ->where('cities.id', $cityId);
            })
            ->first();
    }
}
