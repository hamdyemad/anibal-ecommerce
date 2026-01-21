<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Illuminate\Support\Facades\Log;

class CalculateFinalTotal
{
    /**
     * Handle the pipeline.
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        $subtotal = $context['total_product_price']; // Price before tax
        // Use calculated shipping from CalculateShipping pipeline, or fallback to data
        $shipping = (float) ($data['shipping'] ?? 0);
        $tax = $context['total_tax'] ?? 0;
        $fees = $context['total_fees'] ?? 0;
        $discounts = $context['total_discounts'] ?? 0;
        $pointsCost = $context['points_cost'] ?? 0;

        // Calculate promo discount from subtotal (price before tax)
        $promocode = $context['promo_code'] ?? null;
        $promoDiscount = $promocode ? $this->calculatePromoDiscount($subtotal, $promocode->value, $promocode->type) : 0;

        Log::info('CalculateFinalTotal: Before calculation', [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'fees' => $fees,
            'discounts' => $discounts,
            'promo_discount' => $promoDiscount,
            'points_cost' => $pointsCost
        ]);

        $totalPrice = $subtotal + $shipping + $fees + $tax - $discounts - $promoDiscount - $pointsCost;

        Log::info('CalculateFinalTotal: After calculation', [
            'total_price' => $totalPrice
        ]);

        // Validate that total is not negative
        if ($totalPrice < 0) {
            throw new \Exception(trans('order::order.total_cannot_be_negative', [
                'total' => number_format($totalPrice, 2)
            ]));
        }

        $context['subtotal'] = $subtotal;
        $context['shipping'] = $shipping;
        $context['total_price'] = $totalPrice;
        $context['promo_code_discount'] = $promoDiscount;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }

    private function calculatePromoDiscount($subtotal, $value, $type)
    {
        if ($type === 'amount') {
            return $value;
        } elseif ($type === 'percent') {
            return ($subtotal * $value) / 100;
        }
        return 0;
    }
}
