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
        Schema::table('refund_request_items', function (Blueprint $table) {
            if (!Schema::hasColumn('refund_request_items', 'reason')) {
                $table->text('reason')->nullable()->after('refund_amount');
            }
            if (!Schema::hasColumn('refund_request_items', 'shipping_amount')) {
                $table->decimal('shipping_amount', 10, 2)->default(0)->after('discount_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refund_request_items', function (Blueprint $table) {
            $table->dropColumn(['reason', 'shipping_amount']);
        });
    }
};
