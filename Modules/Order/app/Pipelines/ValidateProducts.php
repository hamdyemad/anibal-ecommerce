<?php

namespace Modules\Order\app\Pipelines;

use App\Exceptions\OrderException;
use Closure;

class ValidateProducts
{
    /**
     * Handle the pipeline.
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Validate products array
        if (empty($data['products']) || !is_array($data['products'])) {
            throw new OrderException(trans('order.validation.products_required'));
        }

        // Validate and normalize each product
        $normalizedProducts = [];
        foreach ($data['products'] as $product) {
            if (empty($product['vendor_product_variant_id']) || empty($product['quantity'])) {
                throw new OrderException(trans('order.invalid_product_data'));
            }

            if ($product['quantity'] <= 0) {
                throw new OrderException(trans('order.invalid_quantity'));
            }

            // Preserve bundle/occasion data from cart, only pass minimal data for new products
            $normalizedProduct = [
                'vendor_product_id' => $product['vendor_product_id'],
                'vendor_product_variant_id' => $product['vendor_product_variant_id'],
                'quantity' => $product['quantity'],
            ];

            // Preserve bundle and occasion data if present
            if (isset($product['type'])) {
                $normalizedProduct['type'] = $product['type'];
            }
            if (isset($product['bundle_id'])) {
                $normalizedProduct['bundle_id'] = $product['bundle_id'];
            }
            if (isset($product['bundle'])) {
                $normalizedProduct['bundle'] = $product['bundle'];
            }
            if (isset($product['occasion_id'])) {
                $normalizedProduct['occasion_id'] = $product['occasion_id'];
            }
            if (isset($product['occasion'])) {
                $normalizedProduct['occasion'] = $product['occasion'];
            }

            $normalizedProducts[] = $normalizedProduct;
        }

        // Store normalized products in context
        $context['products'] = $normalizedProducts;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
