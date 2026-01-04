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
        Schema::table('order_products', function (Blueprint $table) {
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('commission');
            $table->foreignId('stage_id')->nullable()->after('shipping_cost')->constrained('order_stages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropForeign(['stage_id']);
            $table->dropColumn(['shipping_cost', 'stage_id']);
        });
    }
};
