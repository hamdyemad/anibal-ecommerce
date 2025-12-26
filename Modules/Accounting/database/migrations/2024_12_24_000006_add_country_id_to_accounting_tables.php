<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('accounting_entries', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('vendor_id');
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
                $table->index('country_id');
            }
        });
        
        Schema::table('expense_items', function (Blueprint $table) {
            if (!Schema::hasColumn('expense_items', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('active');
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
                $table->index('country_id');
            }
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('receipt_file');
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
                $table->index('country_id');
            }
        });
        
        Schema::table('vendor_balances', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_balances', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('withdrawn_amount');
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
                $table->index('country_id');
            }
        });
    }

    public function down()
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropIndex(['country_id']);
            $table->dropColumn('country_id');
        });
        
        Schema::table('expense_items', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropIndex(['country_id']);
            $table->dropColumn('country_id');
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropIndex(['country_id']);
            $table->dropColumn('country_id');
        });
        
        Schema::table('vendor_balances', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropIndex(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};
