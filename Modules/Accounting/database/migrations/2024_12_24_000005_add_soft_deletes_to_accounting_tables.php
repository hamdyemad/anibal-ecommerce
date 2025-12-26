<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('accounting_entries', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        Schema::table('expense_items', function (Blueprint $table) {
            if (!Schema::hasColumn('expense_items', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        Schema::table('vendor_balances', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_balances', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_entries', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        
        Schema::table('expense_items', function (Blueprint $table) {
            if (Schema::hasColumn('expense_items', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        
        Schema::table('vendor_balances', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_balances', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
