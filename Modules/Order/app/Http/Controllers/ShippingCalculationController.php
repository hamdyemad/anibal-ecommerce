<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Order\app\Http\Requests\CalculateShippingRequest;
use Modules\Order\app\Http\Resources\ShippingCalculationResource;
use Modules\Order\app\Services\ShippingCalculationService;

class ShippingCalculationController extends Controller
{
    public function __construct(
        protected ShippingCalculationService $shippingCalculationService
    ) {
        $this->middleware('can:orders.create')->only(['calculate']);
    }

    /**
     * Calculate shipping cost for cart items (Dashboard)
     * POST /admin/shipping/calculate
     * Supports both existing customers (with address) and external customers (with city_id)
     */
    public function calculate($lang, $countryCode, CalculateShippingRequest $request)
    {
        $customerId = $request->input('customer_id');
        $customerAddressId = $request->input('customer_address_id');
        $cityId = $request->input('city_id');
        $cartItems = $request->input('cart_items');

        // Transform cart items to the format expected by the repository
        $cartItems = $this->transformCartItems($cartItems);

        $result = $this->shippingCalculationService->calculateShipping(
            $customerId,
            $customerAddressId,
            $cartItems,
            $cityId
        );

        return response()->json([
            'success' => true,
            'message' => trans('shipping.calculation_success'),
            'data' => new ShippingCalculationResource($result),
        ]);
    }

    /**
     * Transform cart items from frontend format to repository format
     * Converts category_id/department_id/sub_category_id to type_id based on shipping settings
     */
    private function transformCartItems(array $cartItems): array
    {
        // Get shipping settings to determine type
        $shippingSettings = \Modules\SystemSetting\app\Models\SiteInformation::first();
        $groupByType = 'category'; // default
        if ($shippingSettings?->shipping_allow_departments) {
            $groupByType = 'department';
        } elseif ($shippingSettings?->shipping_allow_sub_categories) {
            $groupByType = 'subcategory';
        } elseif ($shippingSettings?->shipping_allow_categories) {
            $groupByType = 'category';
        }

        \Log::info('Transform cart items - shipping settings', [
            'groupByType' => $groupByType,
            'shipping_allow_departments' => $shippingSettings?->shipping_allow_departments,
            'shipping_allow_sub_categories' => $shippingSettings?->shipping_allow_sub_categories,
            'shipping_allow_categories' => $shippingSettings?->shipping_allow_categories,
        ]);

        $transformedItems = [];
        foreach ($cartItems as $item) {
            // Determine type_id and type_name based on groupByType
            $typeId = null;
            $typeName = null;

            switch ($groupByType) {
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

            \Log::info('Transforming cart item', [
                'original_item' => $item,
                'groupByType' => $groupByType,
                'typeId' => $typeId,
                'typeName' => $typeName,
            ]);

            $transformedItems[] = [
                'type' => $groupByType,
                'type_id' => $typeId,
                'type_name' => $typeName,
                'product_id' => $item['product_id'] ?? null,
                'vendor_id' => $item['vendor_id'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
            ];
        }

        \Log::info('Transformed cart items result', ['transformedItems' => $transformedItems]);

        return $transformedItems;
    }
}
