<?php

namespace Modules\AreaSettings\app\Observers;

use Modules\AreaSettings\app\Models\Region;
use App\Services\CacheService;
use Illuminate\Support\Facades\Log;

class RegionObserver
{
    protected CacheService $cache;
    protected static bool $cacheCleared = false;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the Region "saved" event (fires after create/update and all relations)
     */
    public function saved(Region $region): void
    {
        // Use afterCommit to ensure cache is cleared after transaction completes
        \Illuminate\Support\Facades\DB::afterCommit(function () {
            // Only clear cache once per request cycle
            if (!static::$cacheCleared) {
                $this->clearRegionCache();
                static::$cacheCleared = true;
                
                // Reset flag after current request
                app()->terminating(function () {
                    static::$cacheCleared = false;
                });
            }
        });
    }

    /**
     * Handle the Region "deleted" event.
     */
    public function deleted(Region $region): void
    {
        if (!static::$cacheCleared) {
            $this->clearRegionCache();
            static::$cacheCleared = true;
            
            app()->terminating(function () {
                static::$cacheCleared = false;
            });
        }
    }

    /**
     * Handle the Region "restored" event.
     */
    public function restored(Region $region): void
    {
        if (!static::$cacheCleared) {
            $this->clearRegionCache();
            static::$cacheCleared = true;
            
            app()->terminating(function () {
                static::$cacheCleared = false;
            });
        }
    }

    /**
     * Clear all region-related cache
     */
    protected function clearRegionCache(): void
    {
        Log::info('RegionObserver: Clearing region cache');
        
        $cleared = $this->cache->forgetByPattern('regionapi:*');
        
        Log::info('RegionObserver: Cache cleared', [
            'regionapi_keys' => $cleared
        ]);
    }
}
