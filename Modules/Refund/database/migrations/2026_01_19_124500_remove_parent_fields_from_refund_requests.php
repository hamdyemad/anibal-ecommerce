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
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropForeign(['parent_refund_id']);
            $table->dropColumn(['parent_refund_id', 'is_parent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_refund_id')->nullable()->after('id');
            $table->boolean('is_parent')->default(false)->after('parent_refund_id');
            
            $table->foreign('parent_refund_id')->references('id')->on('refund_requests')->onDelete('cascade');
        });
    }
};
