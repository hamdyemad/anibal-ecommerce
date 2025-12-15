<?php

namespace Modules\Order\app\Services\Api;

use Modules\Order\app\DTOs\CartFilterDTO;
use Modules\Order\app\Interfaces\Api\ShippingCalculationRepositoryInterface;
use Modules\Order\app\Services\Api\CartService;

class ShippingCalculationApiService
{
    protected $shippingCalculationRepository;
    protected $cartService;

    public function __construct(
        ShippingCalculationRepositoryInterface $shippingCalculationRepository,
        CartService $cartService
    ) {
        $this->shippingCalculationRepository = $shippingCalculationRepository;
        $this->cartService = $cartService;
    }

    /**
     * Calculate shipping cost for customer's cart items based on customer address
     */
    public function calculateShippingForCart($customerId, $customerAddressId)
    {
        // Fetch cart items from CartService
        $cartItems = $this->getCartItems($customerId);

        return $this->shippingCalculationRepository->calculateShipping(
            $customerId,
            $customerAddressId,
            $cartItems
        );
    }

    /**
     * Calculate shipping cost for provided cart items
     */
    public function calculateShipping($customerId, $customerAddressId, array $cartItems)
    {
        return $this->shippingCalculationRepository->calculateShipping(
            $customerId,
            $customerAddressId,
            $cartItems
        );
    }

    /**
     * Get cart items formatted for shipping calculation using CartService
     */
    private function getCartItems($customerId): array
    {
        // Get cart items from CartService
        $dto = new CartFilterDTO();
        $cartItems = $this->cartService->getCustomerCart($dto, $customerId);

        // Format cart items for shipping calculation
        return $cartItems->map(function ($item) {
            return [
                'category_id' => $item->vendorProduct->product->category->id,
                'category_name' => $item->vendorProduct->product->category->name,
                'product_id' => $item->vendorProduct->product_id,
                'quantity' => $item->quantity,
            ];
        })->toArray();
    }
}
