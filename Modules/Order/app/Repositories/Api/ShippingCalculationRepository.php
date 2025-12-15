<?php

namespace Modules\Order\app\Repositories\Api;

use Modules\Order\app\Interfaces\Api\ShippingCalculationRepositoryInterface;
use Modules\Customer\app\Models\CustomerAddress;
use Modules\Order\app\Models\Shipping;
use App\Exceptions\OrderException;

class ShippingCalculationRepository implements ShippingCalculationRepositoryInterface
{
    /**
     * Calculate shipping cost for cart items based on customer address
     * Groups items by category and city, adds shipping cost once per group
     */
    public function calculateShipping($customerId, $customerAddressId, array $cartItems)
    {
        // Get customer address
        $address = CustomerAddress::with('city')->findOrFail($customerAddressId);
        
        if ($address->customer_id != $customerId) {
            throw new OrderException(trans('shipping.address_not_found'));
        }

        // Group cart items by category_id
        $itemsByCategory = $this->groupItemsByCategory($cartItems);
        
        // Calculate shipping for each category-city combination
        $shippingBreakdown = [];
        $totalShippingCost = 0;

        foreach ($itemsByCategory as $categoryId => $items) {
            // Get shipping for this category and city
            $shipping = Shipping::where('category_id', $categoryId)
                ->where('city_id', $address->city_id)
                ->where('active', 1)
                ->first();

            if ($shipping) {
                $shippingBreakdown[] = [
                    'category_id' => $categoryId,
                    'category_name' => $items[0]['category_name'] ?? null,
                    'city_id' => $address->city_id,
                    'city_name' => $address->city->name ?? null,
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
            'address' => [
                'id' => $address->id,
                'title' => $address->title,
                'city_id' => $address->city_id,
                'city_name' => $address->city->name ?? null,
            ]
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
