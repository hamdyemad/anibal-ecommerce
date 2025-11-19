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
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('video_link');
        });
        Schema::table('vendor_products', function (Blueprint $table) {
            $table->string('video_link')->after('is_featured')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('slug')->nullable();
            $table->string('video_link')->nullable();
        });
        Schema::table('vendor_products', function (Blueprint $table) {
            $table->dropColumn('video_link');
        });
    }
};
