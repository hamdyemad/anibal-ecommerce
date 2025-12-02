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
        Schema::table('occasion_products', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['vendor_product_id']);
            // Then drop the column
            $table->dropColumn('vendor_product_id');
        });

        Schema::table('occasions', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('occasion_products', function (Blueprint $table) {
            $table->foreignId('vendor_product_id')->nullable()->constrained('vendor_products')->cascadeOnDelete();
        });

        Schema::table('occasions', function (Blueprint $table) {
            $table->string('image')->nullable();
        });
    }
};
