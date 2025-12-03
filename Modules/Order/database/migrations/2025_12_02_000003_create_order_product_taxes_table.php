<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductTaxesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_product_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_product_id')->constrained('order_products')->onDelete('cascade');
            $table->foreignId('tax_id')->constrained('taxes')->onDelete('cascade');
            $table->decimal('percentage', 5, 2);
            $table->timestamps();

            $table->unique(['order_product_id', 'tax_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_taxes');
    }
}
