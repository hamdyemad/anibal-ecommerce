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
    ) {}

    /**
     * Calculate shipping cost for cart items (Dashboard)
     * POST /admin/shipping/calculate
     */
    public function calculate($lang, $countryCode, CalculateShippingRequest $request)
    {
        $customerId = $request->input('customer_id');
        $customerAddressId = $request->input('customer_address_id');
        $cartItems = $request->input('cart_items');

        $result = $this->shippingCalculationService->calculateShipping(
            $customerId,
            $customerAddressId,
            $cartItems
        );

        return response()->json([
            'success' => true,
            'message' => trans('shipping.calculation_success'),
            'data' => new ShippingCalculationResource($result),
        ]);
    }
}
