<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\Order\app\Models\Order;

class UpdateOrder
{
    /**
     * Update existing order with calculated data
     *
     * @param array $payload
     * @param Closure $next
     * @return mixed
     */
    public function handle(array $payload, Closure $next)
    {
        $orderId = $payload['context']['order_id'];
        $order = Order::findOrFail($orderId);

        // Update order with new calculated values
        $order->update([
            'customer_name' => $payload['context']['customer_name'],
            'customer_email' => $payload['context']['customer_email'],
            'customer_phone' => $payload['context']['customer_phone'],
            'customer_address' => $payload['context']['customer_address'],
            'total_product_price' => $payload['context']['total_product_price'],
            'total_tax' => $payload['context']['total_tax'],
            'total_fees' => $payload['context']['total_fees'],
            'total_discounts' => $payload['context']['total_discounts'],
            'shipping_cost' => $payload['context']['shipping_cost'],
            'total_price' => $payload['context']['total_price'],
        ]);

        // Delete existing products and extras to re-sync them
        $order->products()->delete();
        $order->extraFeesDiscounts()->delete();

        $payload['context']['order'] = $order->fresh();

        return $next($payload);
    }
}
