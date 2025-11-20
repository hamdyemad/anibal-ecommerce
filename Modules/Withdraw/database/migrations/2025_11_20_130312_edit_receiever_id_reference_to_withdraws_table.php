<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('withdraws', function (Blueprint $table) {
            // أول حاجة نحذف الـ foreign القديم
            $table->dropForeign(['reciever_id']);

            // لو حابب، ممكن تسيب العمود كما هو أو تغيّر نوعه (غالبًا مش محتاج)
            // $table->unsignedBigInteger('reciever_id')->change();

            // نعمل الـ foreign الجديد على vendors
            $table->foreign('reciever_id')
                ->references('id')
                ->on('vendors')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('withdraws', function (Blueprint $table) {
            $table->dropForeign(['reciever_id']);
            $table->foreign('reciever_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
