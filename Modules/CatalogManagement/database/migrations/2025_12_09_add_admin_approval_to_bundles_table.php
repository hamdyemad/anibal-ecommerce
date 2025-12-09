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
        Schema::table('bundles', function (Blueprint $table) {
            $table->boolean('admin_approval')->default(true)->after('is_active')->comment('Admin approval status for bundles created by vendors');
            $table->text('approval_reason')->nullable()->after('admin_approval')->comment('Reason for rejection or notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->dropColumn(['admin_approval', 'approval_reason']);
        });
    }
};
