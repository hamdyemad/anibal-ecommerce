<?php

namespace Modules\Order\app\Pipelines;

use Modules\SystemSetting\app\Models\UserPoints;

class CalculatePointsUsagePipeline
{
    public function handle($payload, $next)
    {
        \Log::info('CalculatePointsUsagePipeline: Starting', ['payload' => $payload]);

        $data = $payload['data'];
        $customerId = $data['selected_customer_id'] ?? null;
        $usePoints = $data['use_point'] ? true : false;
        $pointsToUse = $data['points_to_use'] ?? $data['point_amount'] ?? 10; // Default to 10 points if not specified
        
        \Log::info('CalculatePointsUsagePipeline: Data extracted', [
            'customer_id' => $customerId,
            'use_points' => $usePoints,
            'points_to_use' => $pointsToUse,
            'available_fields' => array_keys($data)
        ]);

        // Initialize points usage
        $payload['data']['points_used'] = 0;
        $payload['data']['points_cost'] = 0;

        if ($usePoints && $pointsToUse > 0 && $customerId) {
            \Log::info('CalculatePointsUsagePipeline: Processing points usage');

            // Get customer's available points
            $userPoints = UserPoints::where('user_id', $customerId)->first();

            if ($userPoints && $userPoints->total_points >= $pointsToUse) {
                \Log::info('CalculatePointsUsagePipeline: User has sufficient points', [
                    'available' => $userPoints->total_points,
                    'requested' => $pointsToUse
                ]);

                // Calculate how much can be paid with points (1 point = 1 currency unit)
                $pointsValue = $pointsToUse;
                
                // Get total from context - use product price as base
                $totalPrice = $payload['context']['total_product_price'] ?? 100;
                $shipping = $data['shipping'] ?? 0;
                $totalBeforeShipping = $totalPrice - $shipping;
                
                // Points can only be used for product cost, not shipping
                $maxPointsUsable = min($pointsValue, $totalBeforeShipping);

                $payload['data']['points_used'] = $maxPointsUsable;
                $payload['data']['points_cost'] = $maxPointsUsable;
                
                // Store the total price in data for later pipelines
                if (!isset($payload['data']['total_price'])) {
                    $payload['data']['total_price'] = $totalPrice + $shipping;
                }
                // Reduce total_price by points used
                $payload['data']['total_price'] -= $maxPointsUsable;

                // Deduct points from user's account
                $userPoints->total_points -= $maxPointsUsable;
                $userPoints->redeemed_points += $maxPointsUsable;
                $userPoints->save();

                \Log::info('CalculatePointsUsagePipeline: Points processed successfully', [
                    'points_used' => $maxPointsUsable,
                    'new_total' => $payload['data']['total_price']
                ]);
            } else {
                \Log::warning('CalculatePointsUsagePipeline: Insufficient points or no user points record');
            }
        } else {
            \Log::info('CalculatePointsUsagePipeline: Points usage not requested or invalid data');
        }

        return $next($payload);
    }
}
