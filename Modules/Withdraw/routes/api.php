<?php

use Illuminate\Support\Facades\Route;
use Modules\Withdraw\Http\Controllers\WithdrawController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('withdraws', WithdrawController::class)->names('withdraw');
});
