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
        Schema::table('vendor_products', function (Blueprint $table) {
            // Make tax_id nullable (will be auto-assigned from country)
            $table->foreignId('tax_id')->nullable()->change();
            
            // Add tax_percentage to store the tax value at time of creation
            if (!Schema::hasColumn('vendor_products', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->nullable()->after('tax_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_products', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_products', 'tax_percentage')) {
                $table->dropColumn('tax_percentage');
            }
        });
    }
};
