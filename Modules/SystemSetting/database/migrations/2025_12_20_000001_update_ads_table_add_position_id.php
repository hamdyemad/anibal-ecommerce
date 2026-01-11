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
            // Drop the old position enum column if it exists
            if (Schema::hasColumn('ads', 'position')) {
                $table->dropColumn('position');
            }
            
            // Add ad_position_id foreign key
            $table->foreignId('ad_position_id')->nullable()->after('id')->constrained('ads_positions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropForeign(['ad_position_id']);
            $table->dropColumn('ad_position_id');
            
            // Restore the old position enum column
            $table->enum('position', ['header', 'footer', 'sidebar', 'home_banner', 'product_page', 'category_page'])
                  ->after('id')
                  ->comment('Ad position on the page');
        });
    }
};
