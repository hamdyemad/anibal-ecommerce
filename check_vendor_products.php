<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Modules\CatalogManagement\app\Models\VendorProduct;

echo "\nChecking Vendor Products...\n";
echo "============================\n\n";

$products = VendorProduct::with('vendor')->take(10)->get();

echo "Found " . $products->count() . " products\n\n";

foreach ($products as $product) {
    echo "SKU: {$product->sku}\n";
    echo "Vendor ID: " . ($product->vendor_id ?? 'NULL') . "\n";
    echo "Vendor Name: " . ($product->vendor->name ?? 'N/A') . "\n";
    echo "---\n";
}

echo "\nDone!\n";
