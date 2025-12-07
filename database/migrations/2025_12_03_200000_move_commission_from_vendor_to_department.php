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
        // Drop vendor_commission table if it exists
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('vendor_commission');
        Schema::enableForeignKeyConstraints();

        // Add commission columns to departments table
        if (Schema::hasTable('departments') && !Schema::hasColumn('departments', 'commission')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->decimal('commission', 8, 2)->nullable()->default(0)->after('country_id')->comment('Commission percentage for this department');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove commission column from departments
        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'commission')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropColumn('commission');
            });
        }

        // Recreate vendor_commission table
        Schema::create('vendor_commission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->integer('commission');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
