<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vendor_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->decimal('commission_deducted', 10, 2)->default(0);
            $table->decimal('available_balance', 10, 2)->default(0);
            $table->decimal('withdrawn_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->unique('vendor_id');
            $table->index('country_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_balances');
    }
};
