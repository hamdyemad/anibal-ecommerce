<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->enum('type', ['income', 'expense', 'commission', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('commission_amount', 10, 2)->nullable();
            $table->decimal('vendor_amount', 10, 2)->nullable();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->index(['type', 'created_at']);
            $table->index('country_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_entries');
    }
};
