<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('vendor_product_id')->constrained('vendor_products')->onDelete('cascade');
            $table->foreignId('vendor_product_variant_id')->nullable()->constrained('vendor_product_variants')->onDelete('cascade');
            $table->enum('type', ['product', 'bundle', 'occasion'])->default('product');
            $table->foreignId('bundle_id')->nullable()->constrained('bundles')->onDelete('cascade');
            $table->foreignId('occasion_id')->nullable()->constrained('occasions')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('customer_id');
            $table->index('vendor_product_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
