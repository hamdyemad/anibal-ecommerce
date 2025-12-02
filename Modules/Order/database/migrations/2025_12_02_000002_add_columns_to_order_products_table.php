<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->foreignId('vendor_product_id')->nullable()->constrained('vendor_products')->onDelete('set null');
            $table->foreignId('vendor_product_variant_id')->nullable()->constrained('vendor_product_variants')->onDelete('set null');
            $table->integer('quantity')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropForeign(['vendor_product_id']);
            $table->dropForeign(['vendor_product_variant_id']);
            $table->dropColumn(['vendor_product_id', 'vendor_product_variant_id', 'quantity']);
        });
    }
}
