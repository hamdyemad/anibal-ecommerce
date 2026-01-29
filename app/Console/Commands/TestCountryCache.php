<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class TestCountryCache extends Command
{
    protected $signature = 'test:country-cache';
    protected $description = 'Test country cache creation and invalidation';

    public function handle()
    {
        $this->info('=== Testing Country Cache ===');
        $this->newLine();

        $baseUrl = config('app.url');
        $apiUrl = "{$baseUrl}/api/v1/area/countries?per_page=5";

        // Step 1: Check initial cache state
        $this->info('Step 1: Checking initial cache state...');
        $keys = $this->getCacheKeys();
        $this->line("Found {$keys} cache keys");
        $this->newLine();

        // Step 2: Make first API request (should create cache)
        $this->info('Step 2: Making first API request (creates cache)...');
        $start = microtime(true);
        $response1 = Http::get($apiUrl);
        $time1 = round((microtime(true) - $start) * 1000, 2);
        
        if ($response1->successful()) {
            $this->line("✅ Response time: {$time1}ms");
            $data1 = $response1->json();
            $count = count($data1['data'] ?? []);
            $this->line("✅ Countries returned: {$count}");
        } else {
            $this->error("❌ Request failed: " . $response1->status());
            return 1;
        }

        // Check cache was created
        $keys = $this->getCacheKeys();
        $this->line("✅ Cache keys now: {$keys}");
        $this->newLine();

        // Step 3: Make second API request (should use cache)
        $this->info('Step 3: Making second API request (uses cache)...');
        sleep(1); // Small delay
        $start = microtime(true);
        $response2 = Http::get($apiUrl);
        $time2 = round((microtime(true) - $start) * 1000, 2);
        
        if ($response2->successful()) {
            $this->line("✅ Response time: {$time2}ms");
            $improvement = round((($time1 - $time2) / $time1) * 100, 1);
            
            if ($time2 < $time1) {
                $this->line("✅ Cache working! {$improvement}% faster");
            } else {
                $this->warn("⚠️  Second request not faster - cache might not be working");
            }
        }
        $this->newLine();

        // Step 4: Show cache keys
        $this->info('Step 4: Listing cache keys...');
        $this->showCacheKeys();
        $this->newLine();

        // Step 5: Clear cache
        $this->info('Step 5: Clearing cache...');
        $cleared = $this->clearCache();
        $this->line("✅ Cleared {$cleared} cache keys");
        $this->newLine();

        // Step 6: Verify cache cleared
        $this->info('Step 6: Verifying cache cleared...');
        $keys = $this->getCacheKeys();
        if ($keys === 0) {
            $this->line("✅ Cache successfully cleared");
        } else {
            $this->warn("⚠️  Still found {$keys} cache keys");
        }
        $this->newLine();

        // Step 7: Make request after cache clear (should rebuild)
        $this->info('Step 7: Making request after cache clear (rebuilds cache)...');
        $start = microtime(true);
        $response3 = Http::get($apiUrl);
        $time3 = round((microtime(true) - $start) * 1000, 2);
        
        if ($response3->successful()) {
            $this->line("✅ Response time: {$time3}ms");
            $this->line("✅ Cache rebuilt");
        }
        $this->newLine();

        // Final summary
        $this->info('=== Summary ===');
        $this->table(
            ['Request', 'Time (ms)', 'Status'],
            [
                ['First (no cache)', $time1, 'Created cache'],
                ['Second (cached)', $time2, 'Used cache'],
                ['After clear', $time3, 'Rebuilt cache'],
            ]
        );

        $this->newLine();
        $this->info('✅ Country cache test completed!');
        $this->line('To test cache invalidation on update:');
        $this->line('1. Update a country in admin panel');
        $this->line('2. Check logs: tail -f storage/logs/laravel.log | grep CountryObserver');
        $this->line('3. Run this command again to verify cache was cleared');

        return 0;
    }

    private function getCacheKeys(): int
    {
        try {
            // Use cache database (database 1)
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $prefix = config('database.redis.options.prefix', '') . config('cache.prefix', '');
            $keys = $redis->keys($prefix . 'countryapi:*');
            return count($keys);
        } catch (\Exception $e) {
            $this->error("Redis error: " . $e->getMessage());
            return 0;
        }
    }

    private function showCacheKeys(): void
    {
        try {
            // Use cache database (database 1)
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $prefix = config('database.redis.options.prefix', '') . config('cache.prefix', '');
            $keys = $redis->keys($prefix . 'countryapi:*');
            
            if (empty($keys)) {
                $this->line('No cache keys found');
                return;
            }

            foreach ($keys as $key) {
                $ttl = $redis->ttl($key);
                // Remove prefix for display
                $displayKey = str_replace($prefix, '', $key);
                $this->line("  - {$displayKey} (TTL: {$ttl}s)");
            }
        } catch (\Exception $e) {
            $this->error("Redis error: " . $e->getMessage());
        }
    }

    private function clearCache(): int
    {
        try {
            // Use cache database (database 1)
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $prefix = config('database.redis.options.prefix', '') . config('cache.prefix', '');
            $keys = $redis->keys($prefix . 'countryapi:*');
            $count = count($keys);
            
            if ($count > 0) {
                foreach ($keys as $key) {
                    $redis->del($key);
                }
            }
            
            return $count;
        } catch (\Exception $e) {
            $this->error("Redis error: " . $e->getMessage());
            return 0;
        }
    }
}
