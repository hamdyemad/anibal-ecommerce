<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_notifications', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('notifiable_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('admin_notifications', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('user_id')->constrained('vendors')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['user_id', 'vendor_id']);
        });
    }
};
