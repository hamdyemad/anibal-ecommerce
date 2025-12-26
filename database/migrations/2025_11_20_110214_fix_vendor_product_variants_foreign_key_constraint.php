<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check if the table exists
        if (!Schema::hasTable('vendor_product_variants')) {
            return;
        }

        Schema::table('vendor_product_variants', function (Blueprint $table) {
            // Check if the column exists and has the wrong constraint
            if (Schema::hasColumn('vendor_product_variants', 'variant_configuration_id')) {
                // Drop existing foreign key constraints for this column
                try {
                    $table->dropForeign(['variant_configuration_id']);
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }

                // Make the column nullable if it isn't already
                try {
                    $table->unsignedBigInteger('variant_configuration_id')->nullable()->change();
                } catch (\Exception $e) {
                    // Column might already be correct
                }

                // Add the correct foreign key constraint
                try {
                    $table->foreign('variant_configuration_id')
                          ->references('id')
                          ->on('variants_configurations')
                          ->nullOnDelete();
                } catch (\Exception $e) {
                    // Constraint might already exist
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('vendor_product_variants')) {
            Schema::table('vendor_product_variants', function (Blueprint $table) {
                try {
                    $table->dropForeign(['variant_configuration_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });
        }
    }
};
