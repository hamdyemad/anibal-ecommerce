<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounting\app\Http\Controllers\AccountingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('accountings', AccountingController::class)->names('accounting');
});
