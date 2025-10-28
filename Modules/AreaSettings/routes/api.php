<?php

use Illuminate\Support\Facades\Route;
use Modules\AreaSettings\Http\Controllers\AreaSettingsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('areasettings', AreaSettingsController::class)->names('areasettings');
});
