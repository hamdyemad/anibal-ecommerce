<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('customer_address_id')->constrained('customer_addresses')->cascadeOnDelete();
            $table->text('notes');
            $table->string('file')->nullable();
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->text('offer_notes')->nullable();
            $table->timestamp('offer_sent_at')->nullable();
            $table->timestamp('offer_responded_at')->nullable();
            $table->enum('status', ['pending', 'sent_offer', 'accepted_offer', 'rejected_offer', 'order_created', 'archived'])->default('pending');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_quotations');
    }
};
