<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$carts = \Modules\Order\app\Models\Cart::with(['customer', 'vendorProduct.product'])->get();

echo "=== ALL CART ITEMS ===\n";
echo "Total cart items: " . $carts->count() . "\n\n";

foreach ($carts as $cart) {
    echo "Cart ID: {$cart->id}\n";
    echo "Customer ID: {$cart->customer_id}\n";
    echo "Customer Email: " . ($cart->customer->email ?? 'N/A') . "\n";
    echo "Vendor Product ID: {$cart->vendor_product_id}\n";
    echo "Product Name: " . ($cart->vendorProduct->product->name ?? 'N/A') . "\n";
    echo "Quantity: {$cart->quantity}\n";
    echo "---\n";
}
