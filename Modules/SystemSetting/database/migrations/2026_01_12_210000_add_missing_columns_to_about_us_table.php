<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            // Add missing columns for sections 2, 3, 4 sub_section icons
            if (!Schema::hasColumn('about_us', 'section_2_sub_section_1_icon')) {
                $table->string('section_2_sub_section_1_icon')->nullable()->after('section_2_image');
            }
            if (!Schema::hasColumn('about_us', 'section_2_sub_section_2_icon')) {
                $table->string('section_2_sub_section_2_icon')->nullable()->after('section_2_sub_section_1_icon');
            }
            if (!Schema::hasColumn('about_us', 'section_3_sub_section_1_icon')) {
                $table->string('section_3_sub_section_1_icon')->nullable()->after('section_3_image');
            }
            if (!Schema::hasColumn('about_us', 'section_3_sub_section_2_icon')) {
                $table->string('section_3_sub_section_2_icon')->nullable()->after('section_3_sub_section_1_icon');
            }
            if (!Schema::hasColumn('about_us', 'section_4_sub_section_1_icon')) {
                $table->string('section_4_sub_section_1_icon')->nullable()->after('section_4_image');
            }
            if (!Schema::hasColumn('about_us', 'section_4_sub_section_2_icon')) {
                $table->string('section_4_sub_section_2_icon')->nullable()->after('section_4_sub_section_1_icon');
            }
            
            // Remove old video_link column if exists
            if (Schema::hasColumn('about_us', 'section_2_video_link')) {
                $table->dropColumn('section_2_video_link');
            }
        });
    }

    public function down(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            $columns = [
                'section_2_sub_section_1_icon',
                'section_2_sub_section_2_icon',
                'section_3_sub_section_1_icon',
                'section_3_sub_section_2_icon',
                'section_4_sub_section_1_icon',
                'section_4_sub_section_2_icon',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('about_us', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
