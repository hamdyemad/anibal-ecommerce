<?php

use Illuminate\Support\Facades\Route;
use Modules\Refund\app\Http\Controllers\RefundRequestController;
use Modules\Refund\app\Http\Controllers\AdminVendorRefundSettingController;
use Modules\Refund\app\Http\Controllers\VendorRefundSettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Refund Requests Routes
Route::group(['prefix' => 'refunds', 'as' => 'refunds.'], function () {
    Route::get('/', [RefundRequestController::class, 'index'])->name('index');
    Route::get('/datatable', [RefundRequestController::class, 'datatable'])->name('datatable');
    
    // Admin: Manage all vendors' refund settings (must be before dynamic routes)
    Route::group(['prefix' => 'admin-settings', 'as' => 'admin-settings.'], function () {
        Route::get('/', [AdminVendorRefundSettingController::class, 'index'])->name('index');
        Route::get('/datatable', [AdminVendorRefundSettingController::class, 'datatable'])->name('datatable');
        Route::put('/{vendor}/refund-days', [AdminVendorRefundSettingController::class, 'updateRefundDays'])->name('update-refund-days');
        Route::put('/{vendor}/customer-pays-shipping', [AdminVendorRefundSettingController::class, 'updateCustomerPaysShipping'])->name('update-customer-pays-shipping');
    });
    
    // Vendor: Manage own refund settings (must be before dynamic routes)
    Route::get('/settings', [VendorRefundSettingController::class, 'index'])->name('settings');
    Route::put('/settings', [VendorRefundSettingController::class, 'update'])->name('settings.update');
    
    // Dynamic routes (must be last)
    Route::get('/{refundRequest}', [RefundRequestController::class, 'show'])->name('show');
    Route::post('/{refundRequest}/approve', [RefundRequestController::class, 'approve'])->name('approve');
    Route::post('/{refundRequest}/reject', [RefundRequestController::class, 'reject'])->name('reject');
    Route::post('/{refundRequest}/change-status', [RefundRequestController::class, 'changeStatus'])->name('change-status');
    Route::put('/{refundRequest}/notes', [RefundRequestController::class, 'updateNotes'])->name('update-notes');
});
