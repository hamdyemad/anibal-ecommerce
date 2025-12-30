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
        // Create pivot table for vendor_products and taxes
        Schema::create('vendor_product_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_product_id')->constrained('vendor_products')->cascadeOnDelete();
            $table->foreignId('tax_id')->constrained('taxes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['vendor_product_id', 'tax_id']);
        });

        // Remove tax_id from vendor_products
        if (Schema::hasColumn('vendor_products', 'tax_id')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                $table->dropForeign(['tax_id']);
                $table->dropColumn('tax_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the pivot table
        Schema::dropIfExists('vendor_product_taxes');

        // Re-add tax_id to vendor_products
        if (!Schema::hasColumn('vendor_products', 'tax_id')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                $table->foreignId('tax_id')->nullable()->after('vendor_id')->constrained('taxes')->nullOnDelete();
            });
        }
    }
};
