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
        Schema::create('bundle_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained('bundles')->cascadeOnDelete();
            $table->foreignId('vendor_product_variant_id')->constrained('vendor_product_variants')->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->integer('limitation_quantity')->nullable();
            $table->integer('min_quantity')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index('bundle_id');
            $table->index('vendor_product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_products');
    }
};
