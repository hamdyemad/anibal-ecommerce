<?php

use Illuminate\Support\Facades\Route;
use Modules\Withdraw\app\Http\Controllers\WithdrawController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('withdraws', WithdrawController::class)->names('withdraw');
});


