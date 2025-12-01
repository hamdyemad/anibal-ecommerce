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
        Schema::create('order_stages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('color', 7); // Hex color code
            $table->boolean('active')->default(1);
            $table->boolean('is_system')->default(0); // System stages cannot be deleted
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_stages');
    }
};
