<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop model_countries table if it exists
        Schema::dropIfExists('model_countries');

        // List of all tables to add country_id to
        $tables = [
            // Core tables
            'users', 'vendors', 'products', 'categories', 'departments',
            'brands', 'taxes', 'activities', 'sub_categories', 'regions',
            'cities', 'sub_regions', 'vendor_products', 'roles',

            'vendor_requests',
            // CatalogManagement module tables
            'reviews', 'bundle_categories', 'occasions', 'occasion_products',
            'vendor_product_variants', 'vendor_product_variant_stocks',
            'product_variants', 'variants_configurations', 'variants_configurations_keys',

            // Customer module tables
            'customers', 'customer_addresses', 'customer_fcm_tokens',
            'customer_otps', 'customer_password_reset_tokens',

            // Order module tables
            'orders', 'order_products', 'wishlists', 'order_stages',

            'promocodes',
            // Withdraw module tables
            'withdraws',

            // Other module tables
            'attachments', 'languages', 'translations', 'permessions',
            'activity_logs'
        ];

        // Add country_id to all tables
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'country_id')) {
                try {
                    $hasIdColumn = Schema::hasColumn($tableName, 'id');

                    Schema::table($tableName, function (Blueprint $table) use ($hasIdColumn) {
                        if ($hasIdColumn) {
                            $table->unsignedBigInteger('country_id')->nullable()->after('id');
                        } else {
                            $table->unsignedBigInteger('country_id')->nullable()->first();
                        }
                        $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
                    });
                } catch (\Exception $e) {
                    // Skip tables that have issues
                    Log::warning("Could not add country_id to {$tableName}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // List of all tables to drop country_id from
        $tables = [
            // Core tables
            'users', 'vendors', 'products', 'categories', 'departments',
            'brands', 'taxes', 'activities', 'sub_categories', 'regions',
            'cities', 'sub_regions', 'vendor_products', 'roles',

            // CatalogManagement module tables
            'reviews', 'bundle_categories', 'occasions', 'occasion_products',
            'vendor_product_variants', 'vendor_product_variant_stocks',
            'product_variants', 'variant_configurations', 'variant_configurations_keys',

            // Customer module tables
            'customers', 'customer_addresses', 'customer_fcm_tokens',
            'customer_otps', 'customer_password_reset_tokens',

            // Order module tables
            'orders', 'order_products', 'wishlists', 'order_stages',

            // Withdraw module tables
            'withdraw_requests', 'withdraw_transactions',

            // Bank module tables
            'bank_stocks', 'bank_stock_histories',

            // Other module tables
            'attachments', 'languages', 'translations', 'permessions',
            'activity_logs'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'country_id')) {
                try {
                    Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                        // Try to drop foreign key if it exists
                        try {
                            $table->dropForeign(["{$tableName}_country_id_foreign"]);
                        } catch (\Exception $e) {
                            // Foreign key might not exist, continue
                        }
                        $table->dropColumn('country_id');
                    });
                } catch (\Exception $e) {
                    Log::warning("Could not drop country_id from {$tableName}: " . $e->getMessage());
                }
            }
        }
    }
};
