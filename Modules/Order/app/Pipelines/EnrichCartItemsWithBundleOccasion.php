<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Illuminate\Support\Facades\Auth;
use Modules\Order\app\Models\Cart;

class EnrichCartItemsWithBundleOccasion
{
    /**
     * Enrich cart items with bundle and occasion data
     * Fetches bundle/occasion relationships and pricing info
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Get cart items with bundle and occasion relationships
        $cartItems = Cart::where('customer_id', Auth::id())
            ->with(['bundle.bundleProducts', 'occasion.occasionProducts'])
            ->get();

        // Enrich products with bundle/occasion data
        $enrichedProducts = [];
        foreach ($data['products'] as $product) {
            $cartItem = $cartItems->first(function ($item) use ($product) {
                return $item->vendor_product_id === $product['vendor_product_id']
                    && $item->vendor_product_variant_id === $product['vendor_product_variant_id'];
            });

            if ($cartItem) {
                $product['type'] = $cartItem->type ?? 'product';
                $product['bundle_id'] = $cartItem->bundle_id;
                $product['occasion_id'] = $cartItem->occasion_id;
                $product['bundle'] = $cartItem->bundle;
                $product['occasion'] = $cartItem->occasion;
            }

            $enrichedProducts[] = $product;
        }

        $data['products'] = $enrichedProducts;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
