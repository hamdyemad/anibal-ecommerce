<?php

namespace Modules\AreaSettings\app\Observers;

use Modules\AreaSettings\app\Models\City;
use App\Services\CacheService;
use Illuminate\Support\Facades\Log;

class CityObserver
{
    protected CacheService $cache;
    protected static bool $cacheCleared = false;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the City "saved" event (fires after create/update and all relations)
     */
    public function saved(City $city): void
    {
        // Use afterCommit to ensure cache is cleared after transaction completes
        \Illuminate\Support\Facades\DB::afterCommit(function () {
            // Only clear cache once per request cycle
            if (!static::$cacheCleared) {
                $this->clearCityCache();
                static::$cacheCleared = true;
                
                // Reset flag after current request
                app()->terminating(function () {
                    static::$cacheCleared = false;
                });
            }
        });
    }

    /**
     * Handle the City "deleted" event.
     */
    public function deleted(City $city): void
    {
        if (!static::$cacheCleared) {
            $this->clearCityCache();
            static::$cacheCleared = true;
            
            app()->terminating(function () {
                static::$cacheCleared = false;
            });
        }
    }

    /**
     * Handle the City "restored" event.
     */
    public function restored(City $city): void
    {
        if (!static::$cacheCleared) {
            $this->clearCityCache();
            static::$cacheCleared = true;
            
            app()->terminating(function () {
                static::$cacheCleared = false;
            });
        }
    }

    /**
     * Clear all city-related cache
     */
    protected function clearCityCache(): void
    {
        Log::info('CityObserver: Clearing city cache');
        
        $cleared = $this->cache->forgetByPattern('cityapi:*');
        
        Log::info('CityObserver: Cache cleared', [
            'cityapi_keys' => $cleared
        ]);
    }
}
