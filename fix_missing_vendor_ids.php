<?php

/**
 * Fix Missing Vendor IDs in VendorProducts
 * 
 * This script finds VendorProduct records with NULL vendor_id
 * and reports them so you can fix them.
 * 
 * Run: php fix_missing_vendor_ids.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\CatalogManagement\app\Models\VendorProduct;

echo "Checking for VendorProducts with missing vendor_id...\n\n";

$productsWithoutVendor = VendorProduct::whereNull('vendor_id')
    ->orWhere('vendor_id', 0)
    ->with('product')
    ->get();

if ($productsWithoutVendor->isEmpty()) {
    echo "✓ All VendorProducts have vendor_id set!\n";
    exit(0);
}

echo "Found " . $productsWithoutVendor->count() . " products without vendor_id:\n\n";

foreach ($productsWithoutVendor as $vp) {
    echo "ID: {$vp->id}, SKU: {$vp->sku}, Product: " . ($vp->product->title ?? 'N/A') . "\n";
}

echo "\n";
echo "To fix these, you need to:\n";
echo "1. Identify which vendor owns each product\n";
echo "2. Update the vendor_id in the database\n";
echo "\nExample SQL:\n";
echo "UPDATE vendor_products SET vendor_id = YOUR_VENDOR_ID WHERE id IN (" . $productsWithoutVendor->pluck('id')->implode(',') . ");\n";
