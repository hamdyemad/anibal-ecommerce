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
            $table->string('slug')->nullable()->after('id');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
        });

        Schema::table('subregions', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('subregions', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
