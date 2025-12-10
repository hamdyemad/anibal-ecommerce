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
        Schema::create('points_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->boolean('is_active')->default(false);
            $table->decimal('points_value', 10, 2)->default(0);
            $table->decimal('welcome_points', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: one setting per currency
            $table->unique('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_settings');
    }
};
