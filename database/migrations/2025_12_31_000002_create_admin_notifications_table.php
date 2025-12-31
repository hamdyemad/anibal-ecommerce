<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // vendor_request, order, message, quotation_response, etc.
            $table->string('notifiable_type')->nullable(); // Model class
            $table->unsignedBigInteger('notifiable_id')->nullable(); // Model ID
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Target specific admin user
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete(); // Target specific vendor
            $table->string('icon')->default('uil-bell');
            $table->string('color')->default('primary');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->json('data')->nullable(); // Additional data
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'is_read']);
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index(['user_id', 'is_read']);
            $table->index(['vendor_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
