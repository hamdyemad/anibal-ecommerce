<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Predis KEYS command...\n\n";

$redis = \Illuminate\Support\Facades\Redis::connection('cache');

echo "Connection type: " . get_class($redis) . "\n";
echo "Client type: " . config('database.redis.client') . "\n\n";

// Test 1: Get all keys
echo "Test 1: All keys in cache database\n";
$allKeys = $redis->keys('*');
echo "Type: " . gettype($allKeys) . "\n";
echo "Count: " . count($allKeys) . "\n";
print_r($allKeys);
echo "\n";

// Test 2: Search for country keys
echo "Test 2: Search for *countryapi*\n";
$countryKeys = $redis->keys('*countryapi*');
echo "Type: " . gettype($countryKeys) . "\n";
echo "Count: " . count($countryKeys) . "\n";
print_r($countryKeys);
echo "\n";

// Test 3: Search with full pattern
$pattern = 'laravel_database_laravel_cache_countryapi:*';
echo "Test 3: Search for {$pattern}\n";
$patternKeys = $redis->keys($pattern);
echo "Type: " . gettype($patternKeys) . "\n";
echo "Count: " . count($patternKeys) . "\n";
print_r($patternKeys);
echo "\n";

// Test 4: Try command method
echo "Test 4: Using command() method\n";
$commandKeys = $redis->command('keys', ['*countryapi*']);
echo "Type: " . gettype($commandKeys) . "\n";
echo "Count: " . count($commandKeys) . "\n";
print_r($commandKeys);
