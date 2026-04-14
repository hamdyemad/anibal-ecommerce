<?php
/**
 * Clear API Cache Script
 * Run this after adding/updating/deleting categories, departments, subcategories, or brands
 * 
 * Usage: php clear-api-cache.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clear all API caches
$patterns = [
    'api_categories_',
    'departments_',
    'subcategories_',
    'api_brands_',
];

echo "🔍 Searching for cache entries...\n";

$cleared = 0;

// For database cache driver, we need to query the cache table
$cacheDriver = config('cache.default');

if ($cacheDriver === 'database') {
    $cacheTable = config('cache.stores.database.table', 'cache');
    
    foreach ($patterns as $pattern) {
        // Delete all cache entries that start with the pattern
        $deleted = \Illuminate\Support\Facades\DB::table($cacheTable)
            ->where('key', 'like', $pattern . '%')
            ->delete();
        
        $cleared += $deleted;
        echo "   ✓ Cleared {$deleted} entries for pattern: {$pattern}*\n";
    }
} else {
    // For other cache drivers (redis, file, etc.)
    foreach ($patterns as $pattern) {
        $keys = \Illuminate\Support\Facades\Cache::getStore()->getRedis()->keys($pattern . '*');
        
        foreach ($keys as $key) {
            $key = str_replace(\Illuminate\Support\Facades\Cache::getStore()->getPrefix(), '', $key);
            \Illuminate\Support\Facades\Cache::forget($key);
            $cleared++;
        }
        echo "   ✓ Cleared entries for pattern: {$pattern}*\n";
    }
}

echo "\n✅ Total cleared: {$cleared} cache entries\n";
echo "📋 Cache driver: {$cacheDriver}\n";
