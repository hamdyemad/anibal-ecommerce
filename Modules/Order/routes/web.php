<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;
use Modules\Order\app\Http\Controllers\OrderStageController;

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    // Order Stages Management
    Route::prefix('order-stages')->name('order-stages.')->group(function () {
        Route::get('/', [OrderStageController::class, 'index'])->name('index');
        Route::get('/datatable', [OrderStageController::class, 'datatable'])->name('datatable');
        Route::get('/create', [OrderStageController::class, 'create'])->name('create');
        Route::post('/', [OrderStageController::class, 'store'])->name('store');
        Route::get('/{id}', [OrderStageController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [OrderStageController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OrderStageController::class, 'update'])->name('update');
        Route::delete('/{id}', [OrderStageController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [OrderStageController::class, 'toggleStatus'])->name('toggle-status');
    });
});
