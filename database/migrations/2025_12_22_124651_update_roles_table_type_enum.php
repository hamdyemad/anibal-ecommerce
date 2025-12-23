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
        // Drop the old type column
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        // Add the new type column with updated enum values
        Schema::table('roles', function (Blueprint $table) {
            $table->enum('type', ['super_admin', 'admin', 'vendor', 'vendor_user', 'users_vendors', 'other'])->default('other')->after('vendor_id');
        });
        
        // Add is_system_protected column if it doesn't exist
        if (!Schema::hasColumn('roles', 'is_system_protected')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->boolean('is_system_protected')->default(false)->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new type column
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        // Restore the old type column
        Schema::table('roles', function (Blueprint $table) {
            $table->enum('type', ['admin', 'other'])->default('other')->after('vendor_id');
        });
        
        // Drop is_system_protected if it exists
        if (Schema::hasColumn('roles', 'is_system_protected')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('is_system_protected');
            });
        }
    }
};
