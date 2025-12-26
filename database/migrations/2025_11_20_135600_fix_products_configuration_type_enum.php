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
        if (!Schema::hasTable('products')) {
            return;
        }

        // First, update any existing 'with_variants' values to 'variants'
        if (Schema::hasColumn('products', 'configuration_type')) {
            DB::table('products')
                ->where('configuration_type', 'with_variants')
                ->update(['configuration_type' => 'variants']);

            // SQLite doesn't support MODIFY COLUMN with ENUM, so we recreate the column
            if (DB::getDriverName() === 'sqlite') {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropColumn('configuration_type');
                });
                Schema::table('products', function (Blueprint $table) {
                    $table->enum('configuration_type', ['simple', 'variants'])->default('simple');
                });
            } else {
                // MySQL/PostgreSQL
                DB::statement("ALTER TABLE products MODIFY COLUMN configuration_type ENUM('simple', 'variants') DEFAULT 'simple'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        // First, update any existing 'variants' values back to 'with_variants'
        if (Schema::hasColumn('products', 'configuration_type')) {
            DB::table('products')
                ->where('configuration_type', 'variants')
                ->update(['configuration_type' => 'with_variants']);

            // SQLite doesn't support MODIFY COLUMN with ENUM, so we recreate the column
            if (DB::getDriverName() === 'sqlite') {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropColumn('configuration_type');
                });
                Schema::table('products', function (Blueprint $table) {
                    $table->enum('configuration_type', ['simple', 'with_variants'])->default('simple');
                });
            } else {
                // MySQL/PostgreSQL
                DB::statement("ALTER TABLE products MODIFY COLUMN configuration_type ENUM('simple', 'with_variants') DEFAULT 'simple'");
            }
        }
    }
};
