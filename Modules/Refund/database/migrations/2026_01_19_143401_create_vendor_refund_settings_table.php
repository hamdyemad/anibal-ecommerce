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
        Schema::create('vendor_refund_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->boolean('refund_enabled')->default(true)->comment('Enable/disable refunds for this vendor');
            $table->integer('refund_processing_days')->default(7)->comment('Vendor-specific refund days');
            $table->boolean('customer_pays_return_shipping')->default(false)->comment('Customer pays return shipping');
            $table->timestamps();

            // Ensure one setting per vendor
            $table->unique('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_refund_settings');
    }
};
