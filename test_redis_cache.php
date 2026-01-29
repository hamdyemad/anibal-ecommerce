<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$redis = \Illuminate\Support\Facades\Redis::connection('cache');
$prefix = config('database.redis.options.prefix', '') . config('cache.prefix', '');

echo "Prefix: {$prefix}\n";
echo "Looking for pattern: {$prefix}countryapi:*\n\n";

$keys = $redis->keys($prefix . 'countryapi:*');

echo "Found " . count($keys) . " keys:\n";
foreach ($keys as $key) {
    echo "  - {$key}\n";
}
