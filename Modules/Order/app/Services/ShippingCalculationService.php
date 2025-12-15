<?php

namespace Modules\Order\app\Services;

use Modules\Order\app\Interfaces\Api\ShippingCalculationRepositoryInterface;

class ShippingCalculationService
{
    protected $shippingCalculationRepository;

    public function __construct(ShippingCalculationRepositoryInterface $shippingCalculationRepository)
    {
        $this->shippingCalculationRepository = $shippingCalculationRepository;
    }

    /**
     * Calculate shipping cost for cart items based on customer address
     */
    public function calculateShipping($customerId, $customerAddressId, array $cartItems)
    {
        return $this->shippingCalculationRepository->calculateShipping(
            $customerId,
            $customerAddressId,
            $cartItems
        );
    }
}
