<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clean up orphaned products with non-existent sub_category_id
        DB::statement('
            DELETE FROM products
            WHERE sub_category_id IS NOT NULL
            AND sub_category_id NOT IN (SELECT id FROM categories)
        ');

        // Check if foreign key exists and drop it
        $foreignKeyExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'products'
            AND CONSTRAINT_NAME = 'products_sub_category_id_foreign'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        if (!empty($foreignKeyExists)) {
            DB::statement('ALTER TABLE products DROP FOREIGN KEY products_sub_category_id_foreign');
        }

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_category_id')->nullable()->change();
            $table->foreign('sub_category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->unsignedBigInteger('sub_category_id')->nullable(false)->change();
            $table->foreign('sub_category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }
};
