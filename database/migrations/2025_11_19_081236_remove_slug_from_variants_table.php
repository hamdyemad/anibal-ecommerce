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
        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                // Drop unique index first if it exists
                try {
                    $table->dropUnique(['slug']);
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }
                
                if (Schema::hasColumn('product_variants', 'slug')) {
                    $table->dropColumn('slug');
                }
                if (Schema::hasColumn('product_variants', 'video_link')) {
                    $table->dropColumn('video_link');
                }
            });
        }
        
        if (Schema::hasTable('vendor_products')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                if (!Schema::hasColumn('vendor_products', 'video_link')) {
                    $table->string('video_link')->after('is_featured')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                if (!Schema::hasColumn('product_variants', 'slug')) {
                    $table->string('slug')->nullable();
                }
                if (!Schema::hasColumn('product_variants', 'video_link')) {
                    $table->string('video_link')->nullable();
                }
            });
        }
        
        if (Schema::hasTable('vendor_products')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_products', 'video_link')) {
                    $table->dropColumn('video_link');
                }
            });
        }
    }
};
