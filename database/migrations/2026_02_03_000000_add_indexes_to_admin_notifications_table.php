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
        Schema::table('admin_notifications', function (Blueprint $table) {
            // Add indexes for frequently queried columns
            $table->index('type', 'idx_admin_notifications_type');
            $table->index('vendor_id', 'idx_admin_notifications_vendor_id');
            $table->index('created_at', 'idx_admin_notifications_created_at');
            
            // Composite index for common query patterns
            $table->index(['vendor_id', 'type', 'created_at'], 'idx_admin_notifications_vendor_type_created');
        });
        
        Schema::table('admin_notification_views', function (Blueprint $table) {
            // Add composite index for the JOIN in notViewedBy scope
            $table->index(['admin_notification_id', 'user_id'], 'idx_admin_notification_views_notification_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_admin_notifications_type');
            $table->dropIndex('idx_admin_notifications_vendor_id');
            $table->dropIndex('idx_admin_notifications_created_at');
            $table->dropIndex('idx_admin_notifications_vendor_type_created');
        });
        
        Schema::table('admin_notification_views', function (Blueprint $table) {
            $table->dropIndex('idx_admin_notification_views_notification_user');
        });
    }
};
