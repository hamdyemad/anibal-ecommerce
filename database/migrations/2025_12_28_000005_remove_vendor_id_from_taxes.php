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
        if (Schema::hasColumn('taxes', 'vendor_id')) {
            Schema::table('taxes', function (Blueprint $table) {
                $table->dropColumn('vendor_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('taxes', 'vendor_id')) {
            Schema::table('taxes', function (Blueprint $table) {
                $table->string('vendor_id')->nullable()->after('id');
            });
        }
    }
};
