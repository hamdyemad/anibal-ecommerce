<?php

namespace Modules\Order\app\Pipelines;

use App\Exceptions\OrderException;
use Closure;
use Illuminate\Support\Facades\Auth;
use Modules\Order\app\DTOs\CartFilterDTO;
use Modules\Order\app\Services\Api\CartService;

class FetchCartItems
{
    public function __construct(
        private CartService $cartService
    ) {}

    /**
     * Fetch cart items from authenticated user and add to data
     * Includes bundle and occasion data for API checkout
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Get cart items from authenticated user
        $dto = new CartFilterDTO();
        $cartItems = $this->cartService->getCustomerCart($dto, Auth::id());

        // Map cart items with bundle and occasion relationships
        $cart = collect($cartItems)->map(function($item) {
            // Load relationships if they exist
            if ($item->bundle_id) {
                $item->load('bundle.bundleProducts');
            }
            if ($item->occasion_id) {
                $item->load('occasion.occasionProducts');
            }

            // Convert bundle and occasion to arrays with their relationships
            $bundleData = null;
            if ($item->bundle) {
                $bundleData = [
                    'id' => $item->bundle->id,
                    'bundleProducts' => $item->bundle->bundleProducts ? $item->bundle->bundleProducts->toArray() : [],
                ];
            }

            $occasionData = null;
            if ($item->occasion) {
                $occasionData = [
                    'id' => $item->occasion->id,
                    'occasionProducts' => $item->occasion->occasionProducts ? $item->occasion->occasionProducts->toArray() : [],
                ];
            }

            return [
                'vendor_product_id' => $item->vendor_product_id,
                'vendor_product_variant_id' => $item->vendor_product_variant_id,
                'quantity' => $item->quantity,
                'category_id' => $item->vendorProduct->product->category_id ?? null,
                'category_name' => $item->vendorProduct->product->category->name ?? null,
                'department_id' => $item->vendorProduct->product->department_id ?? null,
                'department_name' => $item->vendorProduct->product->department->name ?? null,
                'sub_category_id' => $item->vendorProduct->product->sub_category_id ?? null,
                'sub_category_name' => $item->vendorProduct->product->subCategory->name ?? null,
                'type' => $item->type ?? 'product',
                'bundle_id' => $item->bundle_id,
                'occasion_id' => $item->occasion_id,
                'bundle' => $bundleData,
                'occasion' => $occasionData,
            ];
        })->toArray();

        // Validate cart is not empty
        if (empty($cart)) {
            throw new OrderException(trans('order.cart_is_empty'));
        }

        // Add cart items to data
        $data['products'] = $cart;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
