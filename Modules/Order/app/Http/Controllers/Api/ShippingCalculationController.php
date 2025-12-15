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
     */
    public function calculate(CalculateShippingRequest $request)
    {
        $customerId = auth()->user()->id;
        $customerAddressId = $request->input('customer_address_id');

        $result = $this->shippingCalculationService->calculateShippingForCart(
            $customerId,
            $customerAddressId
        );

        $message = config('responses.success')[app()->getLocale()];

        return $this->sendRes($message, true, new ShippingCalculationResource($result), [], 200);
    }
}
