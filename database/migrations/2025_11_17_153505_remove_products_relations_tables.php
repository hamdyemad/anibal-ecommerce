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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('vendor_product_variant_stocks');
        Schema::dropIfExists('vendor_product_variants');
        Schema::dropIfExists('vendor_products');
        Schema::dropIfExists('product_variant_stocks');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
