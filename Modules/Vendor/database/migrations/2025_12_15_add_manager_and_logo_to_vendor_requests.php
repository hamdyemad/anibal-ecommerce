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
        Schema::table('vendor_requests', function (Blueprint $table) {
            $table->string('manager_name')->nullable()->after('company_name');
            $table->string('company_logo')->nullable()->after('manager_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_requests', function (Blueprint $table) {
            $table->dropColumn(['manager_name', 'company_logo']);
        });
    }
};
