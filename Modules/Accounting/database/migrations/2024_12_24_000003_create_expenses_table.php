<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_item_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->date('expense_date');
            $table->string('receipt_file')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('expense_item_id')->references('id')->on('expense_items')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->index('country_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
