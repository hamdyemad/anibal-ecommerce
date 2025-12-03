<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_phone')->nullable();
            $table->enum('order_from', ['ios', 'android', 'web'])->default('web');
            $table->enum('payment_type', ['cash_on_delivery', 'online'])->default('cash_on_delivery');
            $table->string('customer_promo_code_title')->nullable();
            $table->decimal('customer_promo_code_value', 10, 2)->nullable();
            $table->enum('customer_promo_code_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('total_tax', 10, 2)->default(0);
            $table->decimal('total_product_price', 10, 2)->default(0);
            $table->integer('items_count')->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->foreignId('stage_id')->nullable()->constrained('order_stages')->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->foreignId('region_id')->nullable()->constrained('regions')->onDelete('set null');
            $table->decimal('refunded_amount', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['stage_id']);
            $table->dropForeign(['country_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['region_id']);
            $table->dropColumn([
                'customer_id',
                'customer_name',
                'customer_email',
                'customer_address',
                'customer_phone',
                'order_from',
                'payment_type',
                'customer_promo_code_title',
                'customer_promo_code_value',
                'customer_promo_code_type',
                'shipping',
                'total_tax',
                'total_product_price',
                'items_count',
                'total_price',
                'stage_id',
                'country_id',
                'city_id',
                'region_id',
                'refunded_amount',
            ]);
        });
    }
}
