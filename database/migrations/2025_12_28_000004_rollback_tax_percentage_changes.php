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
        // Remove tax_percentage from countries table
        if (Schema::hasColumn('countries', 'tax_percentage')) {
            Schema::table('countries', function (Blueprint $table) {
                $table->dropColumn('tax_percentage');
            });
        }

        // Remove tax_percentage from vendor_products table
        if (Schema::hasColumn('vendor_products', 'tax_percentage')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                $table->dropColumn('tax_percentage');
            });
        }

        // Recreate taxes table if it doesn't exist
        if (!Schema::hasTable('taxes')) {
            Schema::create('taxes', function (Blueprint $table) {
                $table->id();
                $table->string('vendor_id')->nullable();
                $table->decimal('percentage', 5, 2);
                $table->boolean('is_active')->default(true);
                $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // Re-add tax_id to vendor_products if it doesn't exist
        if (!Schema::hasColumn('vendor_products', 'tax_id')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                $table->foreignId('tax_id')->nullable()->after('vendor_id')->constrained('taxes')->nullOnDelete();
            });
        }

        // Re-add tax_id to order_product_taxes if it doesn't exist
        if (Schema::hasTable('order_product_taxes') && !Schema::hasColumn('order_product_taxes', 'tax_id')) {
            Schema::table('order_product_taxes', function (Blueprint $table) {
                $table->foreignId('tax_id')->nullable()->after('order_product_id')->constrained('taxes')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a rollback migration, no reverse needed
    }
};
