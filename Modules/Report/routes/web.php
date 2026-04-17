<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\app\Http\Controllers\Web\ReportController;

Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        
        // Financial Reports
        Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
        
        // Registered Users Report
        Route::get('reports/registered-users', [ReportController::class, 'registeredUsers'])->name('reports.registered-users');
        Route::get('reports/data/registered-users', [ReportController::class, 'getRegisteredUsersData'])->name('reports.data.registered-users');
        
        // Area Users Report
        Route::get('reports/area-users', [ReportController::class, 'areaUsers'])->name('reports.area-users');
        Route::get('reports/data/area-users', [ReportController::class, 'getAreaUsersData'])->name('reports.data.area-users');
        Route::get('reports/get-cities', [ReportController::class, 'getCities'])->name('reports.get-cities');
        
        // Orders Report
        Route::get('reports/orders', [ReportController::class, 'orders'])->name('reports.orders');
        Route::get('reports/data/orders', [ReportController::class, 'getOrdersData'])->name('reports.data.orders');
        
        // Products Report
        Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
        Route::get('reports/data/products', [ReportController::class, 'getProductsData'])->name('reports.data.products');
        
        // Points Report
        Route::get('reports/points', [ReportController::class, 'points'])->name('reports.points');
        Route::get('reports/data/points', [ReportController::class, 'getPointsData'])->name('reports.data.points');
        
        // Profitability Report
        Route::get('reports/profitability', [ReportController::class, 'profitability'])->name('reports.profitability');
        Route::get('reports/data/profitability', [ReportController::class, 'getProfitabilityData'])->name('reports.profitability.data');
        
        // Sales Analysis Report
        Route::get('reports/sales-analysis', [ReportController::class, 'salesAnalysis'])->name('reports.sales-analysis');
        Route::get('reports/data/sales-analysis', [ReportController::class, 'getSalesAnalysisData'])->name('reports.sales-analysis.data');
        
        // Product Performance Report
        Route::get('reports/product-performance', [ReportController::class, 'productPerformance'])->name('reports.product-performance');
        Route::get('reports/data/product-performance', [ReportController::class, 'getProductPerformanceData'])->name('reports.product-performance.data');
        
        // Customer Analysis Report
        Route::get('reports/customer-analysis', [ReportController::class, 'customerAnalysis'])->name('reports.customer-analysis');
        Route::get('reports/data/customer-analysis', [ReportController::class, 'getCustomerAnalysisData'])->name('reports.customer-analysis.data');
});
