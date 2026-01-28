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
     * @param string $pattern Pattern to match (e.g., 'countries:*')
     * @return int Number of keys deleted
     */
    public function forgetByPattern(string $pattern): int
    {
        if (config('cache.default') !== 'redis') {
            return 0;
        }

        try {
            $redis = Cache::getRedis()->connection();
            $prefix = config('cache.prefix') . ':';
            $keys = $redis->keys($prefix . $pattern);
            
            $count = 0;
            foreach ($keys as $key) {
                $cacheKey = str_replace($prefix, '', $key);
                Cache::forget($cacheKey);
                $count++;
            }
            
            return $count;
        } catch (\Exception $e) {
            \Log::error('Cache pattern delete failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
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
