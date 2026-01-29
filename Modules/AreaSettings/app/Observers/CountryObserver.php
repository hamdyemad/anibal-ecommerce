<?php

namespace Modules\AreaSettings\app\Observers;

use Modules\AreaSettings\app\Models\Country;
use App\Services\CacheService;
use Illuminate\Support\Facades\Log;

class CountryObserver
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the Country "created" event.
     */
    public function created(Country $country): void
    {
        // Don't clear cache here, wait for saved event
    }

    /**
     * Handle the Country "updated" event.
     */
    public function updated(Country $country): void
    {
        // Don't clear cache here, wait for saved event
    }

    /**
     * Handle the Country "saved" event (fires after create/update and all relations)
     */
    public function saved(Country $country): void
    {
        // Use afterCommit to ensure cache is cleared after transaction completes
        \Illuminate\Support\Facades\DB::afterCommit(function () {
            $this->clearCountryCache();
        });
    }

    /**
     * Handle the Country "deleted" event.
     */
    public function deleted(Country $country): void
    {
        $this->clearCountryCache();
    }

    /**
     * Handle the Country "restored" event.
     */
    public function restored(Country $country): void
    {
        $this->clearCountryCache();
    }

    /**
     * Clear all country-related cache
     */
    protected function clearCountryCache(): void
    {
        Log::info('CountryObserver: Clearing country cache');
        
        $cleared = $this->cache->forgetByPattern('countryapi:*');
        
        Log::info('CountryObserver: Cache cleared', [
            'countryapi_keys' => $cleared
        ]);
    }
}
