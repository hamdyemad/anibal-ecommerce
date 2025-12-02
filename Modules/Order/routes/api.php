<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\app\Http\Controllers\Api\WishlistApiController;

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
