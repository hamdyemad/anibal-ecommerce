<?php

namespace Modules\CatalogManagement\app\Observers;

use Modules\CatalogManagement\app\Models\Bundle;
use App\Services\CacheService;

class BundleObserver
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the Bundle "created" event.
     */
    public function created(Bundle $bundle): void
    {
        $this->clearBundleCache();
    }

    /**
     * Handle the Bundle "updated" event.
     */
    public function updated(Bundle $bundle): void
    {
        $this->clearBundleCache();
    }

    /**
     * Handle the Bundle "deleted" event.
     */
    public function deleted(Bundle $bundle): void
    {
        $this->clearBundleCache();
    }

    /**
     * Clear all bundle-related cache
     */
    protected function clearBundleCache(): void
    {
        $this->cache->forgetByPattern('bundleapi:*');
    }
}
