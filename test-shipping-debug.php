<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check shipping settings
$settings = \Modules\SystemSetting\app\Models\SiteInformation::first();
echo "=== SHIPPING SETTINGS ===\n";
echo "shipping_allow_departments: " . ($settings->shipping_allow_departments ?? 'null') . "\n";
echo "shipping_allow_categories: " . ($settings->shipping_allow_categories ?? 'null') . "\n";
echo "shipping_allow_sub_categories: " . ($settings->shipping_allow_sub_categories ?? 'null') . "\n\n";

// Check if there are any shippings configured
$shippings = \Modules\Order\app\Models\Shipping::with(['cities', 'categories', 'departments', 'subCategories'])
    ->where('active', 1)
    ->get();

echo "=== ACTIVE SHIPPINGS ===\n";
echo "Total active shippings: " . $shippings->count() . "\n\n";

foreach ($shippings as $shipping) {
    echo "Shipping ID: {$shipping->id}\n";
    echo "Name: {$shipping->name}\n";
    echo "Cost: {$shipping->cost}\n";
    echo "Cities: " . $shipping->cities->pluck('name')->implode(', ') . "\n";
    echo "Departments: " . $shipping->departments->pluck('name')->implode(', ') . "\n";
    echo "Categories: " . $shipping->categories->pluck('name')->implode(', ') . "\n";
    echo "Sub Categories: " . $shipping->subCategories->pluck('name')->implode(', ') . "\n";
    echo "---\n";
}

// Check shipping_categories pivot table
echo "\n=== SHIPPING_CATEGORIES PIVOT TABLE ===\n";
$pivotData = \DB::table('shipping_categories')->get();
echo "Total records: " . $pivotData->count() . "\n";
foreach ($pivotData as $pivot) {
    echo "Shipping ID: {$pivot->shipping_id}, Type: {$pivot->type}, Type ID: {$pivot->type_id}\n";
}

// Check a specific product's department
echo "\n=== SAMPLE PRODUCT INFO ===\n";
$product = \Modules\CatalogManagement\app\Models\Product::with(['department', 'category', 'subCategory'])->first();
if ($product) {
    echo "Product ID: {$product->id}\n";
    echo "Product Name: {$product->name}\n";
    echo "Department ID: {$product->department_id}\n";
    echo "Department Name: " . ($product->department->name ?? 'N/A') . "\n";
    echo "Category ID: {$product->category_id}\n";
    echo "Category Name: " . ($product->category->name ?? 'N/A') . "\n";
    echo "Sub Category ID: {$product->sub_category_id}\n";
    echo "Sub Category Name: " . ($product->subCategory->name ?? 'N/A') . "\n";
}
