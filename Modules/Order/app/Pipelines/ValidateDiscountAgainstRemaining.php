<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use App\Exceptions\OrderException;
use Illuminate\Support\Facades\Log;

class ValidateDiscountAgainstRemaining
{
    /**
     * Validate that promo code discount doesn't exceed Bnaia's commission (remaining).
     * Bnaia covers promo code discounts from their commission, so promo discount cannot exceed commission.
     * Points are NOT included in this validation as they are customer's earned value, not Bnaia's cost.
     * 
     * Example:
     * - Order total = 4000 EGP
     * - Promo 50% = 2000 EGP discount
     * - Commission 15% = 600 EGP (Bnaia's remaining)
     * - If promo discount (2000) > commission (600) → Error
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Get totals from context
        $totalProductPrice = $context['total_product_price'] ?? 0; // Price before tax
        $totalTax = $context['total_tax'] ?? 0;
        $totalCommission = $context['total_commission'] ?? 0; // This is Bnaia's commission amount
        
        // Calculate total with tax
        $totalWithTax = $totalProductPrice + $totalTax;
        
        // Get promo code discount
        $promoCode = $context['promo_code'] ?? null;
        $promoDiscount = 0;
        if ($promoCode) {
            if ($promoCode->type === 'amount') {
                $promoDiscount = (float) $promoCode->value;
            } elseif ($promoCode->type === 'percent') {
                $promoDiscount = ($totalWithTax * (float) $promoCode->value) / 100;
            }
        }
        
        // Bnaia's remaining is the commission amount
        // Bnaia covers the promo discounts from their commission
        $bnaiaRemaining = $totalCommission;
        
        Log::info('ValidateDiscountAgainstRemaining: Checking discount limits', [
            'total_with_tax' => $totalWithTax,
            'total_commission' => $totalCommission,
            'bnaia_remaining' => $bnaiaRemaining,
            'promo_discount' => $promoDiscount,
        ]);
        
        // Validate: promo discount should not exceed Bnaia's commission (remaining)
        if ($promoDiscount > $bnaiaRemaining) {
            // Get currency for error message
            $currencyCode = 'EGP';
            $customerId = $data['selected_customer_id'] ?? null;
            if ($customerId) {
                $customer = \Modules\Customer\app\Models\Customer::find($customerId);
                if ($customer && $customer->country && $customer->country->currency) {
                    $currencyCode = $customer->country->currency->code ?? 'EGP';
                }
            }
            
            throw new OrderException(
                trans('order::order.discount_exceeds_commission', [
                    'total_discount' => number_format($promoDiscount, 2),
                    'max_discount' => number_format($bnaiaRemaining, 2),
                    'currency' => $currencyCode
                ])
            );
        }
        
        // Store in context for later use
        $context['bnaia_remaining'] = $bnaiaRemaining;
        $context['promo_discount_amount'] = $promoDiscount;
        
        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
