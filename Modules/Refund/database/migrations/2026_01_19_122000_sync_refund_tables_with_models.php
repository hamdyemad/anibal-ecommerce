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
        // Fix refund_requests table
        Schema::table('refund_requests', function (Blueprint $table) {
            // Add missing financial fields if they don't exist
            if (!Schema::hasColumn('refund_requests', 'vendor_fees_amount')) {
                $table->decimal('vendor_fees_amount', 10, 2)->default(0)->after('total_discount_amount');
            }
            if (!Schema::hasColumn('refund_requests', 'vendor_discounts_amount')) {
                $table->decimal('vendor_discounts_amount', 10, 2)->default(0)->after('vendor_fees_amount');
            }
            if (!Schema::hasColumn('refund_requests', 'promo_code_amount')) {
                $table->decimal('promo_code_amount', 10, 2)->default(0)->after('vendor_discounts_amount');
            }
            
            // Handle points_used vs points_value_deducted
            if (!Schema::hasColumn('refund_requests', 'points_used')) {
                $table->decimal('points_used', 10, 2)->default(0)->after('return_shipping_cost');
            }
        });

        // Ensure refund_request_items has all required fields
        Schema::table('refund_request_items', function (Blueprint $table) {
            if (!Schema::hasColumn('refund_request_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('total_price');
            }
            if (!Schema::hasColumn('refund_request_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('refund_request_items', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->default(0)->after('shipping_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropColumn([
                'vendor_fees_amount',
                'vendor_discounts_amount',
                'promo_code_amount',
                'points_used'
            ]);
        });

        Schema::table('refund_request_items', function (Blueprint $table) {
            // We don't drop columns that might have been there originally
        });
    }
};
