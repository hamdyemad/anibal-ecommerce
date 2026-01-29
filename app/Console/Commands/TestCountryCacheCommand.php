<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class TestCountryCacheCommand extends Command
{
    protected $signature = 'test:country-cache';
    protected $description = 'Test country cache creation and clearing';

    public function handle(CacheService $cacheService)
    {
        $this->info('=== Testing Country Cache ===');
        
        // 1. Create a test cache entry
        $this->info('1. Creating test cache entry...');
        $testKey = 'countryapi:test:' . md5('test');
        Cache::put($testKey, ['test' => 'data'], 3600);
        $this->info("   Created: {$testKey}");
        
        // 2. Verify it exists
        $this->info('2. Verifying cache exists...');
        if (Cache::has($testKey)) {
            $this->info('   ✓ Cache entry exists');
        } else {
            $this->error('   ✗ Cache entry NOT found');
            return 1;
        }
        
        // 3. Check Redis directly
        $this->info('3. Checking Redis directly...');
        $redis = \Illuminate\Support\Facades\Redis::connection('cache');
        $keys = $redis->keys('*countryapi*');
        $this->info('   Found ' . count($keys) . ' keys in Redis:');
        foreach ($keys as $key) {
            $this->line('   - ' . $key);
        }
        
        // 4. Test forgetByPattern
        $this->info('4. Testing forgetByPattern...');
        $cleared = $cacheService->forgetByPattern('countryapi:*');
        $this->info("   Cleared {$cleared} keys");
        
        // 5. Verify cache is gone
        $this->info('5. Verifying cache is cleared...');
        if (!Cache::has($testKey)) {
            $this->info('   ✓ Cache successfully cleared');
        } else {
            $this->error('   ✗ Cache still exists!');
            return 1;
        }
        
        // 6. Check Redis again
        $this->info('6. Checking Redis after clear...');
        $keysAfter = $redis->keys('*countryapi*');
        $this->info('   Found ' . count($keysAfter) . ' keys in Redis');
        
        if (count($keysAfter) === 0) {
            $this->info('✓ All tests passed!');
            return 0;
        } else {
            $this->error('✗ Some keys still remain in Redis:');
            foreach ($keysAfter as $key) {
                $this->line('   - ' . $key);
            }
            return 1;
        }
    }
}
