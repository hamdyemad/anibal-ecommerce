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
        // Add to departments table
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'sort_number')) {
                $table->integer('sort_number')->default(0)->after('active');
            }
            if (!Schema::hasColumn('departments', 'view_status')) {
                $table->boolean('view_status')->default(1)->after('sort_number');
            }
        });

        // Add to categories table
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'sort_number')) {
                $table->integer('sort_number')->default(0)->after('active');
            }
            if (!Schema::hasColumn('categories', 'view_status')) {
                $table->boolean('view_status')->default(1)->after('sort_number');
            }
        });

        // Add to sub_categories table
        Schema::table('sub_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_categories', 'sort_number')) {
                $table->integer('sort_number')->default(0)->after('active');
            }
            if (!Schema::hasColumn('sub_categories', 'view_status')) {
                $table->boolean('view_status')->default(1)->after('sort_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['sort_number', 'view_status']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['sort_number', 'view_status']);
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropColumn(['sort_number', 'view_status']);
        });
    }
};
