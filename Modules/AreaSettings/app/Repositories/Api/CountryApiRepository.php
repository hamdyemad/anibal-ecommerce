<?php

namespace Modules\AreaSettings\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use App\Services\CacheService;
use Modules\AreaSettings\app\Actions\CountryQueryAction;
use Modules\AreaSettings\app\Interfaces\Api\CountryApiRepositoryInterface;

class CountryApiRepository implements CountryApiRepositoryInterface
{
    protected CacheService $cache;

    public function __construct(
        private CountryQueryAction $query, 
        private IsPaginatedAction $paginated,
        CacheService $cache
    ) {
        $this->cache = $cache;
    }

    public function getAllCountries(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $perPage = $filters["per_page"] ?? null;
        
        // Create cache key based on filters
        $cacheKey = $this->cache->key('CountryApi', 'all', array_merge($filters, [
            'paginated' => $paginated,
            'per_page' => $perPage
        ]));
        
        return $this->cache->remember($cacheKey, function() use ($filters, $paginated, $perPage) {
            $query = $this->query->handle($filters);
            return $this->paginated->handle($query, $perPage, $paginated);
        }, 3600); // Cache for 1 hour (countries don't change often)
    }

    public function getCountryById($id, array $filters = [])
    {
        // Create cache key for single country
        $cacheKey = $this->cache->key('CountryApi', 'find', array_merge(['id' => $id], $filters));
        
        return $this->cache->remember($cacheKey, function() use ($id, $filters) {
            return $this->query->handle($filters)
                ->with('currency.translations')
                ->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))
                ->firstOrFail();
        }, 3600); // Cache for 1 hour
    }

    /**
     * Clear country API cache
     */
    public function clearCache(): void
    {
        $this->cache->forgetByPattern('countryapi:*');
    }
}

