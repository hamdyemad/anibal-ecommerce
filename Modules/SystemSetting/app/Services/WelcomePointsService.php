<?php

namespace Modules\SystemSetting\app\Services;

use Illuminate\Support\Facades\Log;
use Modules\Customer\app\Models\Customer;
use Modules\SystemSetting\app\Repositories\PointsSettingRepository;
use Modules\SystemSetting\app\Repositories\PointsSystemRepository;

class WelcomePointsService
{
    public function __construct(
        protected PointsSystemRepository $pointsSystemRepository,
        protected PointsSettingRepository $pointsSettingRepository,
        protected UserPointsService $userPointsService
    ) {}

    /**
     * Award welcome points to a new customer
     */
    public function awardWelcomePoints(Customer $customer): ?int
    {
        try {
            // Check if points system is enabled
            $pointsSystem = $this->pointsSystemRepository->getLatest();
            if (!$pointsSystem || !$pointsSystem->is_enabled) {
                Log::info('Points system is disabled, skipping welcome points', [
                    'customer_id' => $customer->id
                ]);
                return null;
            }

            // Get customer's country currency
            if (!$customer->country || !$customer->country->currency) {
                Log::warning('Customer has no country or currency, skipping welcome points', [
                    'customer_id' => $customer->id
                ]);
                return null;
            }

            $currency = $customer->country->currency;

            // Get points settings for this currency
            $pointsSetting = $this->pointsSettingRepository->getByCurrencyId($currency->id);
            if (!$pointsSetting || !$pointsSetting->is_active) {
                Log::info('No active points setting found for currency, skipping welcome points', [
                    'customer_id' => $customer->id,
                    'currency_id' => $currency->id
                ]);
                return null;
            }

            // Get welcome points amount
            $welcomePoints = $pointsSetting->welcome_points ?? 0;
            if ($welcomePoints <= 0) {
                Log::info('Welcome points is zero, skipping', [
                    'customer_id' => $customer->id
                ]);
                return null;
            }

            // Award welcome points using UserPointsService
            $descriptionEn = "Earned {$welcomePoints} points from registration";
            $descriptionAr = "حصلت على {$welcomePoints} نقطة من التسجيل";

            $transaction = $this->userPointsService->addPoints(
                userId: $customer->id,
                points: $welcomePoints,
                transactionableType: Customer::class,
                transactionableId: $customer->id,
                description: $descriptionEn,
                expiresAt: null,
                pointsPerCurrency: null
            );

            // Update Arabic translation
            $transaction->setTranslation('description', 'ar', $descriptionAr);
            $transaction->save();

            Log::info('Welcome points awarded successfully', [
                'customer_id' => $customer->id,
                'points' => $welcomePoints,
                'transaction_id' => $transaction->id
            ]);

            return $welcomePoints;

        } catch (\Exception $e) {
            Log::error('Error awarding welcome points to customer', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Delete customer points when customer is deleted
     */
    public function deleteCustomerPoints(int $customerId): void
    {
        try {
            // The UserPointsTransaction model should handle soft deletes
            // We don't need to do anything here as transactions are kept for audit
            Log::info('Customer deleted, points transactions preserved for audit', [
                'customer_id' => $customerId
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling customer points deletion', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Restore customer points when customer is restored
     */
    public function restoreCustomerPoints(int $customerId): void
    {
        try {
            // Points transactions are preserved, no action needed
            Log::info('Customer restored, points transactions already available', [
                'customer_id' => $customerId
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling customer points restoration', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
