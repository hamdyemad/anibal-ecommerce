<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\app\Http\Controllers\Web\ReportController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        
        // Registered Users Report
        Route::get('reports/registered-users', [ReportController::class, 'registeredUsers'])->name('reports.registered-users');
        Route::get('reports/data/registered-users', [ReportController::class, 'getRegisteredUsersData'])->name('reports.data.registered-users');
        
        // Area Users Report
        Route::get('reports/area-users', [ReportController::class, 'areaUsers'])->name('reports.area-users');
        Route::get('reports/data/area-users', [ReportController::class, 'getAreaUsersData'])->name('reports.data.area-users');
        
        // Orders Report
        Route::get('reports/orders', [ReportController::class, 'orders'])->name('reports.orders');
        Route::get('reports/data/orders', [ReportController::class, 'getOrdersData'])->name('reports.data.orders');
        
        // Products Report
        Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
        Route::get('reports/data/products', [ReportController::class, 'getProductsData'])->name('reports.data.products');
        
        // Points Report
        Route::get('reports/points', [ReportController::class, 'points'])->name('reports.points');
        Route::get('reports/data/points', [ReportController::class, 'getPointsData'])->name('reports.data.points');
    });
});
