<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$start = microtime(true);
$vendors = \Modules\Vendor\app\Models\Vendor::latest()
    ->with(['translations' => function ($query) {
        $query->where('lang_key', 'name');
    }])
    ->get()
    ->map(function ($vendor) {
        $vendor->translation_name = $vendor->translations->first();
        return $vendor;
    });

// simulate the view loop
foreach ($vendors as $vendor) {
    $x = $vendor->id;
    $y = $vendor->name;
}

$end = microtime(true);
echo json_encode([
    'total_time' => $end - $start,
    'count' => count($vendors)
]);
