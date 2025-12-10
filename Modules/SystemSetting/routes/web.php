<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemSetting\app\Http\Controllers\CurrencyController;
use Modules\SystemSetting\app\Http\Controllers\ActivityLogController;
use Modules\SystemSetting\app\Http\Controllers\MessageController;
use Modules\SystemSetting\app\Http\Controllers\PointsSettingController;
use Modules\SystemSetting\app\Http\Controllers\UserPointsController;

Route::group(['prefix' => 'system-settings', 'as' => 'system-settings.'], function() {
    // Currencies
    Route::get('currencies/datatable', [CurrencyController::class, 'datatable'])->name('currencies.datatable');
    Route::resource('currencies', CurrencyController::class);

    // Activity Logs
    Route::get('activity-logs/datatable', [ActivityLogController::class, 'datatable'])->name('activity-logs.datatable');
    Route::get('activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// Points Settings
Route::get('points-settings', [PointsSettingController::class, 'index'])->name('points-settings.index');
Route::post('points-settings', [PointsSettingController::class, 'store'])->name('points-settings.store');
Route::put('points-settings/{id}', [PointsSettingController::class, 'update'])->name('points-settings.update');
Route::post('points-system/toggle-enabled', [PointsSettingController::class, 'togglePointsSystemEnabled'])->name('points-system.toggle-enabled');

// User Points - Admin routes
Route::group(['prefix' => 'user-points', 'as' => 'user-points'], function() {
    Route::get('', [UserPointsController::class, 'index'])->name('.index');
    Route::get('datatable', [UserPointsController::class, 'datatable'])->name('.datatable');
    Route::get('{id}', [UserPointsController::class, 'show'])->name('.show');
    Route::get('{userId}/transactions/datatable', [UserPointsController::class, 'transactions'])->name('user-points.transactions.datatable');
});

// Messages
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function() {
    Route::get('messages/datatable', [MessageController::class, 'datatable'])->name('messages.datatable');
    Route::delete('messages/{id}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('messages/{id}/archive', [MessageController::class, 'archive'])->name('messages.archive');
    Route::get('messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
});
