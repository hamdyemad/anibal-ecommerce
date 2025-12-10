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
        Schema::table('customers', function (Blueprint $table) {
            // Add location fields if they don't exist
            if (!Schema::hasColumn('customers', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('id')->constrained('countries')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('customers', 'city_id')) {
                $table->foreignId('city_id')->nullable()->after('country_id')->constrained('cities')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('customers', 'region_id')) {
                $table->foreignId('region_id')->nullable()->after('city_id')->constrained('regions')->cascadeOnDelete();
            }

            // Add gender field if it doesn't exist
            if (!Schema::hasColumn('customers', 'gender')) {
                $table->enum('gender', ['male', 'female'])->after('city_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop foreign keys if they exist
            try {
                $table->dropForeign(['country_id']);
            } catch (\Exception $e) {
                // Foreign key doesn't exist
            }

            try {
                $table->dropForeign(['city_id']);
            } catch (\Exception $e) {
                // Foreign key doesn't exist
            }

            try {
                $table->dropForeign(['region_id']);
            } catch (\Exception $e) {
                // Foreign key doesn't exist
            }

            // Drop columns if they exist
            if (Schema::hasColumn('customers', 'city_id')) {
                $table->dropColumn('city_id');
            }

            if (Schema::hasColumn('customers', 'region_id')) {
                $table->dropColumn('region_id');
            }

            if (Schema::hasColumn('customers', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};
