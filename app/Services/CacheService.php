<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Default cache TTL in seconds (5 minutes)
     */
    protected int $defaultTTL = 300;

    /**
     * Get data from cache or execute callback and cache the result
     *
     * @param string $key Cache key
     * @param callable $callback Function to execute if cache miss
     * @param int|null $ttl Time to live in seconds (null = use default)
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? $this->defaultTTL;
        
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Get data from cache
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Cache::get($key, $default);
    }

    /**
     * Store data in cache
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl Time to live in seconds
     * @return bool
     */
    public function put(string $key, $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTTL;
        
        return Cache::put($key, $value, $ttl);
    }

    /**
     * Check if key exists in cache
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Remove data from cache
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Clear cache by pattern (works with Redis)
     *
     * @param string $pattern Pattern to match (e.g., 'countryapi:*')
     * @return int Number of keys deleted
     */
    public function forgetByPattern(string $pattern): int
    {
        if (config('cache.default') !== 'redis') {
            return 0;
        }

        try {
            // Use the cache connection (database 1) instead of default connection
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            
            // Get the full prefix (database prefix + cache prefix)
            $databasePrefix = config('database.redis.options.prefix', '');
            $cachePrefix = config('cache.prefix', '');
            
            // Build full prefix
            $fullPrefix = $databasePrefix . $cachePrefix;
            
            // For Predis, we need to use wildcards around the pattern
            // because exact patterns with colons don't work properly
            $searchPattern = '*' . $pattern;
            
            // Get keys - use wildcard pattern that works with Predis
            $keysResult = $redis->keys($searchPattern);
            
            // Convert to array if it's a Collection
            $keys = $keysResult instanceof \Illuminate\Support\Collection 
                ? $keysResult->all() 
                : (is_array($keysResult) ? $keysResult : []);
            
            // Filter keys to only include those with our full prefix
            $filteredKeys = array_filter($keys, function($key) use ($fullPrefix, $pattern) {
                // Remove the wildcard from pattern for matching
                $cleanPattern = str_replace('*', '', $pattern);
                return strpos($key, $fullPrefix . $cleanPattern) === 0;
            });
            
            \Log::info('CacheService: forgetByPattern', [
                'pattern' => $pattern,
                'database_prefix' => $databasePrefix,
                'cache_prefix' => $cachePrefix,
                'full_prefix' => $fullPrefix,
                'search_pattern' => $searchPattern,
                'keys_found_total' => count($keys),
                'keys_found_filtered' => count($filteredKeys),
                'filtered_keys' => array_values($filteredKeys)
            ]);
            
            $count = 0;
            foreach ($filteredKeys as $fullKey) {
                // The key from Redis includes: database_prefix + cache_prefix + actual_key
                // We need to remove BOTH prefixes because Cache::forget() will add them back
                $cacheKey = str_replace($fullPrefix, '', $fullKey);
                
                \Log::info('CacheService: Attempting to delete key', [
                    'full_key' => $fullKey,
                    'cache_key' => $cacheKey,
                    'full_prefix' => $fullPrefix
                ]);
                
                // Use Laravel's Cache::forget which handles prefixes correctly
                $deleted = Cache::forget($cacheKey);
                
                \Log::info('CacheService: Delete result', [
                    'cache_key' => $cacheKey,
                    'deleted' => $deleted
                ]);
                
                if ($deleted) {
                    $count++;
                }
            }
            
            \Log::info('CacheService: Keys deleted', [
                'count' => $count
            ]);
            
            return $count;
        } catch (\Exception $e) {
            \Log::error('Cache pattern delete failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0;
        }
    }

    /**
     * Clear all cache
     *
     * @return bool
     */
    public function flush(): bool
    {
        return Cache::flush();
    }

    /**
     * Generate cache key for model
     *
     * @param string $model Model name (e.g., 'Country')
     * @param string $method Method name (e.g., 'all', 'find')
     * @param array $params Additional parameters
     * @return string
     */
    public function key(string $model, string $method, array $params = []): string
    {
        $paramsString = empty($params) ? '' : ':' . md5(json_encode($params));
        return strtolower($model) . ':' . $method . $paramsString;
    }

    /**
     * Set default TTL
     *
     * @param int $seconds
     * @return self
     */
    public function setDefaultTTL(int $seconds): self
    {
        $this->defaultTTL = $seconds;
        return $this;
    }
}
