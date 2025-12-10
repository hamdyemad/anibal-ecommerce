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
        Schema::table('user_points_transactions', function (Blueprint $table) {
            // Drop the foreign key constraint if it exists
            try {
                $table->dropForeign(['user_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }

            // Drop the description column if it exists
            if (Schema::hasColumn('user_points_transactions', 'description')) {
                $table->dropColumn('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_points_transactions', function (Blueprint $table) {
            // Restore the foreign key constraint
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Restore the description column
            $table->text('description')->nullable();
        });
    }
};
