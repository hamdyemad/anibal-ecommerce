<?php

use App\Http\Controllers\Api\v1\CountryApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('countries')->group(function () {
    Route::get('/', [CountryApiController::class, 'index'])->name('api.countries.index');
    Route::get('/active', [CountryApiController::class, 'active'])->name('api.countries.active');
    Route::get('/{id}', [CountryApiController::class, 'show'])->name('api.countries.show');
    Route::post('/', [CountryApiController::class, 'store'])->name('api.countries.store');
    Route::put('/{id}', [CountryApiController::class, 'update'])->name('api.countries.update');
    Route::delete('/{id}', [CountryApiController::class, 'destroy'])->name('api.countries.destroy');
    Route::patch('/{id}/toggle-status', [CountryApiController::class, 'toggleStatus'])->name('api.countries.toggle-status');
});
