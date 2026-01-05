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
        Schema::table('ads', function (Blueprint $table) {
            if (!Schema::hasColumn('ads', 'mobile_width')) {
                $table->unsignedInteger('mobile_width')->nullable()->after('link')->comment('Mobile ad width in pixels');
            }
            if (!Schema::hasColumn('ads', 'mobile_height')) {
                $table->unsignedInteger('mobile_height')->nullable()->after('mobile_width')->comment('Mobile ad height in pixels');
            }
            if (!Schema::hasColumn('ads', 'website_width')) {
                $table->unsignedInteger('website_width')->nullable()->after('mobile_height')->comment('Website ad width in pixels');
            }
            if (!Schema::hasColumn('ads', 'website_height')) {
                $table->unsignedInteger('website_height')->nullable()->after('website_width')->comment('Website ad height in pixels');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (Schema::hasColumn('ads', 'mobile_width')) {
                $table->dropColumn('mobile_width');
            }
            if (Schema::hasColumn('ads', 'mobile_height')) {
                $table->dropColumn('mobile_height');
            }
            if (Schema::hasColumn('ads', 'website_width')) {
                $table->dropColumn('website_width');
            }
            if (Schema::hasColumn('ads', 'website_height')) {
                $table->dropColumn('website_height');
            }
        });
    }
};
