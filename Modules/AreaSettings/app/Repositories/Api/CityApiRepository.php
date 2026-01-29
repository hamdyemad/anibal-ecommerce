<?php

namespace Modules\AreaSettings\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use App\Services\CacheService;
use Modules\AreaSettings\app\Actions\CityQueryAction;
use Modules\AreaSettings\app\Interfaces\Api\CityApiRepositoryInterface;

class CityApiRepository implements CityApiRepositoryInterface
{
    protected CacheService $cache;

    public function __construct(
        private CityQueryAction $query, 
        private IsPaginatedAction $paginated,
        CacheService $cache
    ) {
        $this->cache = $cache;
    }

    public function getAllCities(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $perPage = $filters["per_page"] ?? null;
        
        // Create cache key based on filters
        $cacheKey = $this->cache->key('CityApi', 'all', array_merge($filters, [
            'paginated' => $paginated,
            'per_page' => $perPage
        ]));
        
        return $this->cache->remember($cacheKey, function() use ($filters, $paginated, $perPage) {
            $query = $this->query->handle($filters);
            return $this->paginated->handle($query, $perPage, $paginated);
        }, 3600); // Cache for 1 hour
    }

    public function getCitiesByCountry($id, array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $perPage = $filters["per_page"] ?? null;
        
        // Create cache key based on country ID and filters
        $cacheKey = $this->cache->key('CityApi', 'by_country', array_merge($filters, [
            'country_id' => $id,
            'paginated' => $paginated,
            'per_page' => $perPage
        ]));
        
        return $this->cache->remember($cacheKey, function() use ($id, $filters, $paginated, $perPage) {
            $query = $this->query->handle($filters)->whereHas('country', function($q) use ($id) {
                $q->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->active();
            });
            
            return $this->paginated->handle($query, $perPage, $paginated);
        }, 3600); // Cache for 1 hour
    }

    /**
     * Clear city API cache
     */
    public function clearCache(): void
    {
        $this->cache->forgetByPattern('cityapi:*');
    }
}
