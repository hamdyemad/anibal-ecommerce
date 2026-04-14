<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

trait ClearsApiCache
{
    /**
     * Boot the trait and register model events
     */
    protected static function bootClearsApiCache()
    {
        // Clear cache after creating
        static::created(function ($model) {
            $model->clearRelatedCache();
        });

        // Clear cache after updating
        static::updated(function ($model) {
            $model->clearRelatedCache();
        });

        // Clear cache after deleting
        static::deleted(function ($model) {
            $model->clearRelatedCache();
        });

        // Clear cache after restoring (soft delete)
        static::restored(function ($model) {
            $model->clearRelatedCache();
        });
    }

    /**
     * Clear related API cache
     */
    public function clearRelatedCache()
    {
        $patterns = $this->getCachePatterns();
        
        $cacheDriver = config('cache.default');

        if ($cacheDriver === 'database') {
            $cacheTable = config('cache.stores.database.table', 'cache');
            
            foreach ($patterns as $pattern) {
                DB::table($cacheTable)
                    ->where('key', 'like', $pattern . '%')
                    ->delete();
            }
        } else {
            // For other cache drivers (redis, file, etc.)
            foreach ($patterns as $pattern) {
                // Clear all keys matching pattern
                Cache::flush(); // Simplified for non-database drivers
            }
        }

        \Log::info('API Cache cleared', [
            'model' => get_class($this),
            'patterns' => $patterns,
        ]);
    }

    /**
     * Get cache patterns to clear
     * Override this method in your model to specify custom patterns
     */
    protected function getCachePatterns(): array
    {
        // Default patterns - override in model if needed
        return [];
    }
}
