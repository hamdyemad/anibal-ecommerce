<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\Order\app\Interfaces\OrderRepositoryInterface;

class SyncOrderProducts
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}


    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        $order = $context['order'];
        $productsData = $context['products_data'];
        $productShipping = $context['product_shipping'] ?? [];

        // Merge shipping costs into products data
        foreach ($productsData as &$product) {
            $vendorProductId = $product['vendor_product_id'];
            if (isset($productShipping[$vendorProductId])) {
                $product['shipping_cost'] = $productShipping[$vendorProductId]['shipping_cost'];
            } else {
                $product['shipping_cost'] = 0;
            }
        }
        unset($product);

        $this->orderRepository->syncOrderProducts($order, $productsData);

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
