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
        Schema::create('refund_request_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_request_id')->constrained('refund_requests')->onDelete('cascade');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_request_histories');
    }
};
