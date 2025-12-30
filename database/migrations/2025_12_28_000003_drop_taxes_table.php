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
        // First, remove the foreign key constraint from order_product_taxes if it exists
        if (Schema::hasTable('order_product_taxes') && Schema::hasColumn('order_product_taxes', 'tax_id')) {
            Schema::table('order_product_taxes', function (Blueprint $table) {
                // Drop foreign key if exists
                try {
                    $table->dropForeign(['tax_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('tax_id');
            });
        }

        // Remove tax_id from vendor_products if it exists
        if (Schema::hasTable('vendor_products') && Schema::hasColumn('vendor_products', 'tax_id')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                try {
                    $table->dropForeign(['tax_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('tax_id');
            });
        }

        // Drop the taxes table
        Schema::dropIfExists('taxes');

        // Remove taxes permissions from the database
        \DB::table('permessions')->where('key', 'like', 'taxes.%')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate taxes table
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_id')->nullable();
            $table->decimal('percentage', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // Re-add tax_id to vendor_products
        if (Schema::hasTable('vendor_products') && !Schema::hasColumn('vendor_products', 'tax_id')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                $table->foreignId('tax_id')->nullable()->after('vendor_id')->constrained('taxes')->nullOnDelete();
            });
        }
    }
};
