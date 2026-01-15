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
        if (!Schema::hasTable('push_notification_customer_views')) {
            Schema::create('push_notification_customer_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('push_notification_id')->constrained('push_notifications')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['push_notification_id', 'customer_id'], 'pn_customer_view_unique');
                $table->index(['push_notification_id']);
                $table->index(['customer_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notification_customer_views');
    }
};
