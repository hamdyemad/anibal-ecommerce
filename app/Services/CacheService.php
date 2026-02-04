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
     * Clear cache by pattern (works with Redis and Database)
     *
     * @param string $pattern Pattern to match (e.g., 'countryapi:*')
     * @return int Number of keys deleted
     */
    public function forgetByPattern(string $pattern): int
    {
        $cacheDriver = config('cache.default');
        
        // Handle Redis cache
        if ($cacheDriver === 'redis') {
            return $this->forgetByPatternRedis($pattern);
        }
        
        // Handle Database cache
        if ($cacheDriver === 'database') {
            return $this->forgetByPatternDatabase($pattern);
        }
        
        // Handle File cache
        if ($cacheDriver === 'file') {
            return $this->forgetByPatternFile($pattern);
        }
        
        // Unsupported cache driver
        \Log::warning('CacheService: forgetByPattern not supported for cache driver', [
            'driver' => $cacheDriver,
            'pattern' => $pattern
        ]);
        
        return 0;
    }
    
    /**
     * Clear cache by pattern for Redis driver
     *
     * @param string $pattern
     * @return int
     */
    protected function forgetByPatternRedis(string $pattern): int
    {
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
            
            \Log::info('CacheService: forgetByPattern (Redis)', [
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
            
            \Log::info('CacheService: Keys deleted (Redis)', [
                'count' => $count
            ]);
            
            return $count;
        } catch (\Exception $e) {
            \Log::error('Cache pattern delete failed (Redis)', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0;
        }
    }
    
    /**
     * Clear cache by pattern for Database driver
     *
     * @param string $pattern
     * @return int
     */
    protected function forgetByPatternDatabase(string $pattern): int
    {
        try {
            $cacheTable = config('cache.stores.database.table', 'cache');
            $cachePrefix = config('cache.prefix', '');
            
            // Convert pattern to SQL LIKE pattern
            // 'countryapi:*' becomes 'countryapi:%'
            $likePattern = str_replace('*', '%', $pattern);
            
            // Add cache prefix if configured
            if ($cachePrefix) {
                $likePattern = $cachePrefix . $likePattern;
            }
            
            \Log::info('CacheService: forgetByPattern (Database)', [
                'pattern' => $pattern,
                'like_pattern' => $likePattern,
                'cache_prefix' => $cachePrefix,
                'table' => $cacheTable
            ]);
            
            // Delete matching cache entries
            $deleted = \Illuminate\Support\Facades\DB::table($cacheTable)
                ->where('key', 'LIKE', $likePattern)
                ->delete();
            
            \Log::info('CacheService: Keys deleted (Database)', [
                'count' => $deleted
            ]);
            
            return $deleted;
        } catch (\Exception $e) {
            \Log::error('Cache pattern delete failed (Database)', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0;
        }
    }
    
    /**
     * Clear cache by pattern for File driver
     *
     * @param string $pattern
     * @return int
     */
    protected function forgetByPatternFile(string $pattern): int
    {
        try {
            $cachePath = config('cache.stores.file.path', storage_path('framework/cache/data'));
            $cachePrefix = config('cache.prefix', '');
            
            // Convert pattern to regex
            // 'countryapi:*' becomes '/countryapi:.*/
            $regexPattern = '/^' . preg_quote($cachePrefix, '/') . str_replace('\*', '.*', preg_quote($pattern, '/')) . '/';
            
            \Log::info('CacheService: forgetByPattern (File)', [
                'pattern' => $pattern,
                'regex_pattern' => $regexPattern,
                'cache_prefix' => $cachePrefix,
                'cache_path' => $cachePath
            ]);
            
            $count = 0;
            
            if (!is_dir($cachePath)) {
                \Log::warning('CacheService: Cache directory not found', [
                    'path' => $cachePath
                ]);
                return 0;
            }
            
            // Recursively scan cache directory
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($cachePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    // Read file to get cache key
                    $contents = file_get_contents($file->getPathname());
                    
                    // Laravel file cache format: expiration timestamp + serialized data
                    // Extract the key from the filename or contents
                    $filename = $file->getFilename();
                    
                    // Check if filename matches pattern
                    if (preg_match($regexPattern, $filename)) {
                        if (unlink($file->getPathname())) {
                            $count++;
                        }
                    }
                }
            }
            
            \Log::info('CacheService: Keys deleted (File)', [
                'count' => $count
            ]);
            
            return $count;
        } catch (\Exception $e) {
            \Log::error('Cache pattern delete failed (File)', [
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
