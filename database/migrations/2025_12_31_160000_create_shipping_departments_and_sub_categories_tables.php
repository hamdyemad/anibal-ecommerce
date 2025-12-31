<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create shipping_departments pivot table
        if (!Schema::hasTable('shipping_departments')) {
            Schema::create('shipping_departments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shipping_id')->constrained('shippings')->onDelete('cascade');
                $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['shipping_id', 'department_id']);
            });
        }

        // Create shipping_sub_categories pivot table
        if (!Schema::hasTable('shipping_sub_categories')) {
            Schema::create('shipping_sub_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shipping_id')->constrained('shippings')->onDelete('cascade');
                $table->foreignId('sub_category_id')->constrained('sub_categories')->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['shipping_id', 'sub_category_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_sub_categories');
        Schema::dropIfExists('shipping_departments');
    }
};
