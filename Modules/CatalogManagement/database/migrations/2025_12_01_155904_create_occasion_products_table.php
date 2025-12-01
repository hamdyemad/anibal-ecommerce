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
        Schema::create('occasion_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('occasion_id')->constrained('occasions')->onDelete('cascade');
            $table->foreignId('vendor_product_id')->constrained('vendor_products')->onDelete('cascade');
            $table->foreignId('vendor_product_variant_id')->nullable()->constrained('vendor_product_variants')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['occasion_id', 'vendor_product_id', 'vendor_product_variant_id'], 'occasion_product_variant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occasion_products');
    }
};
