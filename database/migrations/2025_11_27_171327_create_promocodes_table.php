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
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('maximum_of_use')->default(0);
            $table->enum('type', ['percent', 'amount']);
            $table->decimal('value', 10, 2);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->enum('dedicated_to', ['all', 'male', 'female'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promocodes');
    }
};
