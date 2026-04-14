<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$indexes = \Illuminate\Support\Facades\DB::select('SHOW INDEX FROM translations');
file_put_contents('public/indexes.json', json_encode($indexes, JSON_PRETTY_PRINT));
