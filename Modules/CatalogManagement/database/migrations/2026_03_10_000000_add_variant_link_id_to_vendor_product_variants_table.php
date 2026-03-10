<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_product_variants', function (Blueprint $table) {
            // Add variant_link_id to track the specific parent-child relationship
            // This is needed when a variant configuration is linked to multiple parents
            $table->foreignId('variant_link_id')
                ->nullable()
                ->after('variant_configuration_id')
                ->constrained('variants_configurations_links')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_product_variants', function (Blueprint $table) {
            $table->dropForeign(['variant_link_id']);
            $table->dropColumn('variant_link_id');
        });
    }
};
