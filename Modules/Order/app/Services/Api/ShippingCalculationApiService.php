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

        \Log::info('Raw cart items from CartService', ['count' => $cartItems->count()]);

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

        \Log::info('Shipping settings for cart', [
            'groupByType' => $groupByType,
            'shipping_allow_departments' => $shippingSettings?->shipping_allow_departments,
            'shipping_allow_sub_categories' => $shippingSettings?->shipping_allow_sub_categories,
            'shipping_allow_categories' => $shippingSettings?->shipping_allow_categories,
        ]);

        // Format cart items for shipping calculation
        $formatted = $cartItems->map(function ($item) use ($groupByType) {
            $product = $item->vendorProduct->product;
            $vendorProduct = $item->vendorProduct;
            
            // Determine type_id and type_name based on groupByType
            $typeId = null;
            $typeName = null;

            switch ($groupByType) {
                case 'department':
                    $typeId = $product->department_id;
                    $typeName = $product->department?->getTranslation('name', app()->getLocale());
                    break;
                case 'subcategory':
                    $typeId = $product->sub_category_id;
                    $typeName = $product->subCategory?->getTranslation('name', app()->getLocale());
                    break;
                case 'category':
                default:
                    $typeId = $product->category_id;
                    $typeName = $product->category?->getTranslation('name', app()->getLocale());
                    break;
            }
            
            $formattedItem = [
                'type' => $groupByType,
                'type_id' => $typeId,
                'type_name' => $typeName,
                'category_id' => $product->category_id,
                'category_name' => $product->category?->getTranslation('name', app()->getLocale()),
                'department_id' => $product->department_id,
                'department_name' => $product->department?->getTranslation('name', app()->getLocale()),
                'sub_category_id' => $product->sub_category_id,
                'sub_category_name' => $product->subCategory?->getTranslation('name', app()->getLocale()),
                'product_id' => $vendorProduct->id, // vendor_product_id
                'vendor_id' => $vendorProduct->vendor_id,
                'quantity' => $item->quantity,
            ];
            
            \Log::info('Formatted cart item', $formattedItem);
            
            return $formattedItem;
        })->toArray();
        
        \Log::info('All formatted cart items', ['items' => $formatted]);
        
        return $formatted;
    }
}
