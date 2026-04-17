<?php

namespace Modules\Customer\app\Observers;

use Illuminate\Support\Facades\Log;
use Modules\Customer\app\Models\Customer;
use Modules\SystemSetting\app\Services\WelcomePointsService;

class CustomerObserver
{
    public function __construct(
        protected WelcomePointsService $welcomePointsService
    ) {}

    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        // Award welcome points to new customer
        // This is handled by WelcomePointsService which uses proper service/repository layers
        $this->welcomePointsService->awardWelcomePoints($customer);
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
        // Handle customer points when customer is soft deleted
        // Points transactions are preserved for audit purposes
        try {
            $this->welcomePointsService->deleteCustomerPoints($customer->id);
        } catch (\Exception $e) {
            Log::error('Error handling customer points deletion in observer', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        // Handle customer points when customer is restored
        try {
            $this->welcomePointsService->restoreCustomerPoints($customer->id);
        } catch (\Exception $e) {
            Log::error('Error handling customer points restoration in observer', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        // When customer is force deleted, points transactions are also removed
        // This is handled automatically by database cascading or model events
        Log::info('Customer force deleted, points transactions will be handled by cascade', [
            'customer_id' => $customer->id
        ]);
    }
}
