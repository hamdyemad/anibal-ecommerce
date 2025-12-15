<?php

namespace Modules\Order\app\Interfaces\Api;

interface ShippingCalculationRepositoryInterface
{
    /**
     * Calculate shipping cost for cart items based on customer address
     */
    public function calculateShipping($customerId, $customerAddressId, array $cartItems);
}
