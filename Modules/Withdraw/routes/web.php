<?php

use Illuminate\Support\Facades\Route;
use Modules\Withdraw\app\Http\Controllers\WithdrawController;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function() {
    Route::get('withdraw/send-money', [WithdrawController::class, "sendMoney"])->name("sendMoney");
    Route::get('withdraw/allTransactionsDatabase', [WithdrawController::class, "allTransactionsDatabase"])->name("allTransactionsDatabase");
    Route::get('withdraw/all-transactions', [WithdrawController::class, "allTransactions"])->name("allTransactions");

    Route::get('get-vendor-balance/{vendor_id}', [WithdrawController::class, 'getVendorBalance'])->name('getVendorBalance');
    Route::post('send-money-to-vendor-action', [WithdrawController::class, 'sendMoneyToVendorAction'])->name('sendMoneyToVendorAction');
});
