<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes on critical columns for query performance optimization.
     */
    public function up(): void
    {
        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            // Index on created_at for date range queries
            if (!$this->hasIndex('orders', 'orders_created_at_index')) {
                $table->index('created_at', 'orders_created_at_index');
            }
            
            // Unique index on order_number for fast lookups
            if (!$this->hasIndex('orders', 'orders_order_number_unique')) {
                $table->unique('order_number', 'orders_order_number_unique');
            }
            
            // Index on customer_id if not already indexed by foreign key
            if (!$this->hasIndex('orders', 'orders_customer_id_index')) {
                $table->index('customer_id', 'orders_customer_id_index');
            }
            
            // Index on stage_id for status filtering
            if (!$this->hasIndex('orders', 'orders_stage_id_index')) {
                $table->index('stage_id', 'orders_stage_id_index');
            }
        });

        // Order products table indexes
        Schema::table('order_products', function (Blueprint $table) {
            // Index on vendor_id for vendor-specific queries
            if (!$this->hasIndex('order_products', 'order_products_vendor_id_index')) {
                $table->index('vendor_id', 'order_products_vendor_id_index');
            }
            
            // Index on order_id for order lookups
            if (!$this->hasIndex('order_products', 'order_products_order_id_index')) {
                $table->index('order_id', 'order_products_order_id_index');
            }
        });

        // Vendors table indexes
        Schema::table('vendors', function (Blueprint $table) {
            // Index on user_id for user-vendor lookups
            if (!$this->hasIndex('vendors', 'vendors_user_id_index')) {
                $table->index('user_id', 'vendors_user_id_index');
            }
            
            // Index on active for filtering active vendors
            if (!$this->hasIndex('vendors', 'vendors_active_index')) {
                $table->index('active', 'vendors_active_index');
            }
        });

        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            // Index on department_id for department filtering
            if (!$this->hasIndex('products', 'products_department_id_index')) {
                $table->index('department_id', 'products_department_id_index');
            }
            
            // Index on category_id for category filtering
            if (!$this->hasIndex('products', 'products_category_id_index')) {
                $table->index('category_id', 'products_category_id_index');
            }
            
            // Index on sub_category_id for sub-category filtering
            if (!$this->hasIndex('products', 'products_sub_category_id_index')) {
                $table->index('sub_category_id', 'products_sub_category_id_index');
            }
            
            // Index on brand_id for brand filtering
            if (!$this->hasIndex('products', 'products_brand_id_index')) {
                $table->index('brand_id', 'products_brand_id_index');
            }
        });

        // Vendor products table indexes
        Schema::table('vendor_products', function (Blueprint $table) {
            // Index on vendor_id
            if (!$this->hasIndex('vendor_products', 'vendor_products_vendor_id_index')) {
                $table->index('vendor_id', 'vendor_products_vendor_id_index');
            }
            
            // Index on product_id
            if (!$this->hasIndex('vendor_products', 'vendor_products_product_id_index')) {
                $table->index('product_id', 'vendor_products_product_id_index');
            }
            
            // Index on is_active for filtering
            if (!$this->hasIndex('vendor_products', 'vendor_products_is_active_index')) {
                $table->index('is_active', 'vendor_products_is_active_index');
            }
            
            // Index on status for filtering
            if (!$this->hasIndex('vendor_products', 'vendor_products_status_index')) {
                $table->index('status', 'vendor_products_status_index');
            }
        });

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            // Index on department_id
            if (!$this->hasIndex('categories', 'categories_department_id_index')) {
                $table->index('department_id', 'categories_department_id_index');
            }
            
            // Index on active
            if (!$this->hasIndex('categories', 'categories_active_index')) {
                $table->index('active', 'categories_active_index');
            }
        });

        // Sub categories table indexes
        Schema::table('sub_categories', function (Blueprint $table) {
            // Index on category_id
            if (!$this->hasIndex('sub_categories', 'sub_categories_category_id_index')) {
                $table->index('category_id', 'sub_categories_category_id_index');
            }
            
            // Index on active
            if (!$this->hasIndex('sub_categories', 'sub_categories_active_index')) {
                $table->index('active', 'sub_categories_active_index');
            }
        });

        // Customers table indexes
        Schema::table('customers', function (Blueprint $table) {
            // Index on status for filtering active customers
            if (!$this->hasIndex('customers', 'customers_status_index')) {
                $table->index('status', 'customers_status_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_created_at_index');
            $table->dropUnique('orders_order_number_unique');
            $table->dropIndex('orders_customer_id_index');
            $table->dropIndex('orders_stage_id_index');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->dropIndex('order_products_vendor_id_index');
            $table->dropIndex('order_products_order_id_index');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex('vendors_user_id_index');
            $table->dropIndex('vendors_active_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_department_id_index');
            $table->dropIndex('products_category_id_index');
            $table->dropIndex('products_sub_category_id_index');
            $table->dropIndex('products_brand_id_index');
        });

        Schema::table('vendor_products', function (Blueprint $table) {
            $table->dropIndex('vendor_products_vendor_id_index');
            $table->dropIndex('vendor_products_product_id_index');
            $table->dropIndex('vendor_products_is_active_index');
            $table->dropIndex('vendor_products_status_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_department_id_index');
            $table->dropIndex('categories_active_index');
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropIndex('sub_categories_category_id_index');
            $table->dropIndex('sub_categories_active_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_status_index');
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if ($index['name'] === $indexName) {
                return true;
            }
        }
        return false;
    }
};
