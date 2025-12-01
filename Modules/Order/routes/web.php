<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\app\Http\Controllers\OrderStageController;

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    // Order Stages Management
    Route::prefix('order-stages')->name('order-stages.')->group(function () {
        Route::get('/datatable', [OrderStageController::class, 'datatable'])->name('datatable');
        Route::post('/{id}/toggle-status', [OrderStageController::class, 'toggleStatus'])->name('toggle-status');
    });
    Route::resource('order-stages', OrderStageController::class);
});
