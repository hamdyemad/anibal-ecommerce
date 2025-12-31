<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_information', function (Blueprint $table) {
            $table->boolean('shipping_allow_departments')->default(false)->after('id');
            $table->boolean('shipping_allow_categories')->default(true)->after('shipping_allow_departments');
            $table->boolean('shipping_allow_sub_categories')->default(false)->after('shipping_allow_categories');
        });
    }

    public function down(): void
    {
        Schema::table('site_information', function (Blueprint $table) {
            $table->dropColumn(['shipping_allow_departments', 'shipping_allow_categories', 'shipping_allow_sub_categories']);
        });
    }
};
