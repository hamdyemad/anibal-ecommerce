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
        Schema::create('admin_notification_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_notification_id')->constrained('admin_notifications')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('viewed_at')->useCurrent();
            $table->timestamps();
            
            // Prevent duplicate views
            $table->unique(['admin_notification_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notification_views');
    }
};
