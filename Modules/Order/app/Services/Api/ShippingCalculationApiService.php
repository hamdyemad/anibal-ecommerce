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
     * Calculate shipping cost for customer's cart items based on customer address or city
     */
    public function calculateShippingForCart($customerId, $customerAddressId = null, $cityId = null)
    {
        // Fetch cart items from CartService
        $cartItems = $this->getCartItems($customerId);

        return $this->shippingCalculationRepository->calculateShipping(
            $customerId,
            $customerAddressId,
            $cartItems,
            $cityId
        );
    }

    /**
     * Calculate shipping cost for a city (for guests without cart)
     * Returns available shipping options for the city
     */
    public function calculateShippingForCity($cityId)
    {
        // For guests, return empty cart with city info
        return $this->shippingCalculationRepository->calculateShipping(
            null,
            null,
            [], // empty cart
            $cityId
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
     * Simplified - no category/department grouping
     */
    private function getCartItems($customerId): array
    {
        // Get cart items from CartService
        $dto = new CartFilterDTO();
        $cartItems = $this->cartService->getCustomerCart($dto, $customerId);

        \Log::info('Cart items for shipping', ['count' => $cartItems->count()]);

        // Format cart items for shipping calculation (simplified)
        $formatted = $cartItems->map(function ($item) {
            $vendorProduct = $item->vendorProduct;
            
            return [
                'product_id' => $vendorProduct->id, // vendor_product_id
                'vendor_id' => $vendorProduct->vendor_id,
                'quantity' => $item->quantity,
            ];
        })->toArray();
        
        \Log::info('Formatted cart items', ['items' => $formatted]);
        
        return $formatted;
    }
}
