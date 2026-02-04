<?php

namespace Modules\AreaSettings\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use App\Services\CacheService;
use Modules\AreaSettings\app\Actions\RegionQueryAction;
use Modules\AreaSettings\app\Interfaces\Api\RegionApiRepositoryInterface;

class RegionApiRepository implements RegionApiRepositoryInterface
{
    protected CacheService $cache;

    public function __construct(
        private RegionQueryAction $query, 
        private IsPaginatedAction $paginated,
        CacheService $cache
    ) {
        $this->cache = $cache;
    }

    public function getAllRegions(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $perPage = $filters["per_page"] ?? null;
        
        // Create cache key based on filters
        $cacheKey = $this->cache->key('RegionApi', 'all', array_merge($filters, [
            'paginated' => $paginated,
            'per_page' => $perPage
        ]));
        
        return $this->cache->remember($cacheKey, function() use ($filters, $paginated, $perPage) {
            $query = $this->query->handle($filters);
            return $this->paginated->handle($query, $perPage, $paginated);
        }, 3600); // Cache for 1 hour
    }

    public function getRegionsByCity($id, array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $perPage = $filters["per_page"] ?? null;
        
        // Create cache key based on city ID and filters
        $cacheKey = $this->cache->key('RegionApi', 'by_city', array_merge($filters, [
            'city_id' => $id,
            'paginated' => $paginated,
            'per_page' => $perPage
        ]));
        
        return $this->cache->remember($cacheKey, function() use ($id, $filters, $paginated, $perPage) {
            $query = $this->query->handle($filters)->whereHas('city', function($q) use ($id) {
                $q->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->active();
            });
            
            return $this->paginated->handle($query, $perPage, $paginated);
        }, 3600); // Cache for 1 hour
    }

    /**
     * Clear region API cache
     */
    public function clearCache(): void
    {
        // Use CacheService which now supports all cache drivers (Redis, Database, File)
        $deleted = $this->cache->forgetByPattern('regionapi:*');
        \Log::info('RegionApiRepository: Cleared cache', [
            'pattern' => 'regionapi:*',
            'keys_deleted' => $deleted,
            'cache_driver' => config('cache.default')
        ]);
    }
}
