<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('roles') && Schema::hasColumn('roles', 'type')) {
            // SQLite doesn't support MODIFY COLUMN, so we'll recreate the table
            if (DB::getDriverName() === 'sqlite') {
                // For SQLite, we need to recreate the table
                Schema::table('roles', function (Blueprint $table) {
                    $table->dropColumn('type');
                });
                Schema::table('roles', function (Blueprint $table) {
                    $table->enum('type', ['super_admin', 'admin', 'vendor', 'other'])->default('other');
                });
            } else {
                // For MySQL
                DB::statement("ALTER TABLE roles MODIFY COLUMN type ENUM('super_admin', 'admin', 'vendor', 'other') DEFAULT 'other'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('roles') && Schema::hasColumn('roles', 'type')) {
            if (DB::getDriverName() === 'sqlite') {
                Schema::table('roles', function (Blueprint $table) {
                    $table->dropColumn('type');
                });
                Schema::table('roles', function (Blueprint $table) {
                    $table->enum('type', ['admin', 'other'])->default('other');
                });
            } else {
                DB::statement("ALTER TABLE roles MODIFY COLUMN type ENUM('admin', 'other') DEFAULT 'other'");
            }
        }
    }
};
