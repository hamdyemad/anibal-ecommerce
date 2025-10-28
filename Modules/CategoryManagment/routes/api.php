<?php

use Illuminate\Support\Facades\Route;
use Modules\CategoryManagment\Http\Controllers\CategoryManagmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('categorymanagments', CategoryManagmentController::class)->names('categorymanagment');
});
