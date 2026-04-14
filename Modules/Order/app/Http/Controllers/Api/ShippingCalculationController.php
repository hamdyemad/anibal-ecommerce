<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Modules\Order\app\Http\Requests\Api\CalculateShippingRequest;
use Modules\Order\app\Http\Resources\Api\ShippingCalculationResource;
use Modules\Order\app\Services\Api\ShippingCalculationApiService;

class ShippingCalculationController extends Controller
{
    use Res;

    protected $shippingCalculationService;

    public function __construct(ShippingCalculationApiService $shippingCalculationService)
    {
        $this->shippingCalculationService = $shippingCalculationService;
    }

    /**
     * Calculate shipping cost for cart items
     * POST /api/shipping/calculate
     * Supports both authenticated users (with cart) and guests (with city_id only)
     */
    public function calculate(CalculateShippingRequest $request)
    {
        // Get authenticated user if available (optional)
        $user = auth()->guard('sanctum')->user();
        $customerId = $user?->id;
        $customerAddressId = $request->input('customer_address_id');
        $cityId = $request->input('city_id');

        \Log::info('Shipping calculation API called', [
            'customer_id' => $customerId,
            'customer_address_id' => $customerAddressId,
            'city_id' => $cityId,
            'auth_user' => $user?->email ?? 'guest',
        ]);

        // For guests, only city_id is required (no cart calculation)
        if (!$customerId && $cityId) {
            // Guest user - return shipping info for city only
            $result = $this->shippingCalculationService->calculateShippingForCity($cityId);
        } else {
            // Authenticated user - calculate shipping for cart
            $result = $this->shippingCalculationService->calculateShippingForCart(
                $customerId,
                $customerAddressId,
                $cityId
            );
        }

        \Log::info('Shipping calculation result', $result);

        $message = config('responses.success')[app()->getLocale()];

        return $this->sendRes($message, true, new ShippingCalculationResource($result), [], 200);
    }
}
