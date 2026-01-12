<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->enum('platform', ['website', 'mobile'])->default('website');
            
            // Section 1
            $table->string('section_1_image')->nullable();
            $table->string('section_1_sub_section_1_icon')->nullable();
            $table->string('section_1_sub_section_2_icon')->nullable();
            
            // Section 2
            $table->string('section_2_image')->nullable();
            $table->string('section_2_sub_section_1_icon')->nullable();
            $table->string('section_2_sub_section_2_icon')->nullable();
            
            // Section 3
            $table->string('section_3_image')->nullable();
            $table->string('section_3_sub_section_1_icon')->nullable();
            $table->string('section_3_sub_section_2_icon')->nullable();
            
            // Section 4
            $table->string('section_4_image')->nullable();
            $table->string('section_4_sub_section_1_icon')->nullable();
            $table->string('section_4_sub_section_2_icon')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
