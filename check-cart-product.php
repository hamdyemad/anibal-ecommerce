<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check the product with vendor_product_id 190 (from your cart response)
$vendorProduct = \Modules\CatalogManagement\app\Models\VendorProduct::with(['product.department', 'product.category', 'product.subCategory'])
    ->find(190);

if ($vendorProduct) {
    echo "=== VENDOR PRODUCT INFO ===\n";
    echo "Vendor Product ID: {$vendorProduct->id}\n";
    echo "Vendor ID: {$vendorProduct->vendor_id}\n";
    echo "Product ID: {$vendorProduct->product_id}\n";
    echo "Product Name: {$vendorProduct->product->name}\n";
    echo "Department ID: " . ($vendorProduct->product->department_id ?? 'NULL') . "\n";
    echo "Department Name: " . ($vendorProduct->product->department->name ?? 'N/A') . "\n";
    echo "Category ID: " . ($vendorProduct->product->category_id ?? 'NULL') . "\n";
    echo "Category Name: " . ($vendorProduct->product->category->name ?? 'N/A') . "\n";
    echo "Sub Category ID: " . ($vendorProduct->product->sub_category_id ?? 'NULL') . "\n";
    echo "Sub Category Name: " . ($vendorProduct->product->subCategory->name ?? 'N/A') . "\n";
} else {
    echo "Vendor Product ID 190 not found\n";
}

// Also check cart items for customer
echo "\n=== CHECKING CART ===\n";
$cartItems = \Modules\Order\app\Models\Cart::with(['vendorProduct.product.department'])
    ->where('customer_id', 1) // Adjust customer ID
    ->get();

echo "Cart items count: " . $cartItems->count() . "\n";
foreach ($cartItems as $cart) {
    echo "Cart ID: {$cart->id}\n";
    echo "Vendor Product ID: {$cart->vendor_product_id}\n";
    echo "Product Name: {$cart->vendorProduct->product->name}\n";
    echo "Department ID: " . ($cart->vendorProduct->product->department_id ?? 'NULL') . "\n";
    echo "Department Name: " . ($cart->vendorProduct->product->department->name ?? 'N/A') . "\n";
    echo "---\n";
}
