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
        Schema::table('variants_configurations_links', function (Blueprint $table) {
            $table->json('path')->nullable()->after('child_config_id')->comment('Complete hierarchy path as JSON array of config IDs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variants_configurations_links', function (Blueprint $table) {
            $table->dropColumn('path');
        });
    }
};
