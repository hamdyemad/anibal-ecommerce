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
        Schema::table('countries', function (Blueprint $table) {
            // Add index on code column for faster lookups by country code
            if (!Schema::hasColumn('countries', 'code')) {
                return; // Skip if table doesn't exist
            }
            $table->index('code', 'idx_countries_code');

            // Add index on default column for faster default country lookup
            $table->index('default', 'idx_countries_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropIndex('idx_countries_code');
            $table->dropIndex('idx_countries_default');
        });
    }
};
