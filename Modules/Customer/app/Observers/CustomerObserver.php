<?php

namespace Modules\Customer\app\Observers;

use Illuminate\Support\Facades\Log;
use Modules\Customer\app\Models\Customer;
use Modules\SystemSetting\app\Models\UserPoints;
use Modules\SystemSetting\app\Models\PointsSetting;
use Modules\SystemSetting\app\Models\PointsSystem;
use Modules\SystemSetting\app\Models\UserPointsTransaction;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        try {

            $pointSystem = PointsSystem::latest()->first();
            if ($pointSystem->is_enabled) {

                // Get customer's country
                $currency = $customer->country->currency;
                // Get points settings for this currency
                $pointsSetting = PointsSetting::where('currency_id', $currency->id)->first();
                if (!$pointsSetting) {
                    return; // No points setting found, skip
                }

                // Create customer points record with welcome bonus
                $welcomePoints = $pointsSetting->welcome_points ?? 0;

                $userPoint = UserPoints::create([
                    'user_id' => $customer->id,
                    'total_points' => $welcomePoints,
                    'earned_points' => $welcomePoints,
                    'redeemed_points' => 0,
                    'expired_points' => 0,
                ]);

                // Create transaction record for the welcome points
                $descriptionEn = "Earned {$welcomePoints} points from registration";
                $descriptionAr = "حصلت على {$welcomePoints} نقطة من التسجيل";

                $transaction = $userPoint->transactions()->create([
                    'user_id' => $customer->id,
                    'points' => $welcomePoints,
                    'type' => 'earned',
                ]);
                // Store English translation
                $transaction->setTranslation('description', 'en', $descriptionEn);
                // Store Arabic translation
                $transaction->setTranslation('description', 'ar', $descriptionAr);
            }
        } catch (\Exception $e) {
            // Silently fail - don't block customer creation
            Log::error('Error creating user points for customer ' . $customer->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        // Soft delete user points when customer is deleted
        try {
            UserPoints::where('user_id', $customer->id)->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting user points for customer ' . $customer->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        // Restore user points when customer is restored
        try {
            UserPoints::where('user_id', $customer->id)->restore();
        } catch (\Exception $e) {
            Log::error('Error restoring user points for customer ' . $customer->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        // Force delete user points when customer is force deleted
        try {
            UserPoints::where('user_id', $customer->id)->forceDelete();
        } catch (\Exception $e) {
            Log::error('Error force deleting user points for customer ' . $customer->id . ': ' . $e->getMessage());
        }
    }
}
