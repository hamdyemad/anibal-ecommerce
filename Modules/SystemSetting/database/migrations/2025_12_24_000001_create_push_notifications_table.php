<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('push_notifications')) {
            Schema::create('push_notifications', function (Blueprint $table) {
                $table->id();
                $table->enum('type', ['all', 'specific'])->default('all');
                $table->json('title'); // {en: '', ar: ''}
                $table->json('description'); // {en: '', ar: ''}
                $table->string('image')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('push_notification_customers')) {
            Schema::create('push_notification_customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('push_notification_id')->constrained('push_notifications')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['push_notification_id', 'customer_id'], 'pn_customer_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notification_customers');
        Schema::dropIfExists('push_notifications');
    }
};
