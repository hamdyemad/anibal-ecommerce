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
            $product = $item->vendorProduct->product;
            return [
                'category_id' => $product->category_id,
                'category_name' => $product->category->name ?? null,
                'department_id' => $product->department_id,
                'department_name' => $product->department->name ?? null,
                'sub_category_id' => $product->sub_category_id,
                'sub_category_name' => $product->subCategory->name ?? null,
                'product_id' => $product->id,
                'quantity' => $item->quantity,
            ];
        })->toArray();
    }
}
