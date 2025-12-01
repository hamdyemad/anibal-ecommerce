<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\app\Http\Controllers\OrderStageController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'adminGuard'])->group(function () {
    // Order Stages Management

    // Custom routes (must be defined before resource routes)
    Route::get('order-stages/datatable', [OrderStageController::class, 'datatable'])->name('order-stages.datatable');
    Route::post('order-stages/{id}/toggle-status', [OrderStageController::class, 'toggleStatus'])->name('order-stages.toggle-status');

    // Resource routes
    Route::resource('order-stages', OrderStageController::class);
});
