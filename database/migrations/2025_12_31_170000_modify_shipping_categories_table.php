<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_categories', function (Blueprint $table) {
            // Rename category_id to type_id
            $table->dropForeign(['category_id']);
            $table->renameColumn('category_id', 'type_id');
            
            // Add type enum column
            $table->enum('type', ['category', 'department', 'subcategory'])->default('category')->after('shipping_id');
        });

        // Drop the old pivot tables since we're consolidating into shipping_categories
        Schema::dropIfExists('shipping_departments');
        Schema::dropIfExists('shipping_sub_categories');
    }

    public function down(): void
    {
        Schema::table('shipping_categories', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->renameColumn('type_id', 'category_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        // Recreate the old pivot tables
        Schema::create('shipping_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_id')->constrained('shippings')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['shipping_id', 'department_id']);
        });

        Schema::create('shipping_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_id')->constrained('shippings')->onDelete('cascade');
            $table->foreignId('sub_category_id')->constrained('sub_categories')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['shipping_id', 'sub_category_id']);
        });
    }
};
