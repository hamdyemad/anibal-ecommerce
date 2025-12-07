<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\app\Http\Controllers\Api\WishlistApiController;
use Modules\Order\app\Http\Controllers\Api\OrderStageApiController;

// Public API routes (no authentication required)
Route::prefix('order-stages')->group(function () {
    Route::get('/', [OrderStageApiController::class, 'index'])->name('order-stages.index');
    // Route::get('/{id}', [OrderStageApiController::class, 'show'])->name('order-stages.show');
});

// Order routes
Route::prefix('orders')->group(function () {
    Route::get('{orderId}/allowed-stages', [OrderStageApiController::class, 'allowedStages'])->name('orders.allowed-stages');
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Wishlist API routes
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistApiController::class, 'list'])->name('wishlist.list');
        Route::post('/add', [WishlistApiController::class, 'add'])->name('wishlist.add');
        Route::post('/remove', [WishlistApiController::class, 'remove'])->name('wishlist.remove');
        Route::post('/clear', [WishlistApiController::class, 'clear'])->name('wishlist.clear');
        Route::get('/check', [WishlistApiController::class, 'check'])->name('wishlist.check');
        Route::get('/count', [WishlistApiController::class, 'count'])->name('wishlist.count');
    });
});
