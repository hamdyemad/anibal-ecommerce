<?php

use Illuminate\Support\Facades\Route;
use Modules\AreaSettings\app\Http\Controllers\Api\CountryApiController;
use Modules\AreaSettings\app\Http\Controllers\Api\CityApiController;
use Modules\AreaSettings\app\Http\Controllers\Api\RegionApiController;
use Modules\AreaSettings\app\Http\Controllers\Api\SubRegionApiController;

Route::prefix('area')->group(function () {
    Route::apiResource('countries', CountryApiController::class);
    Route::get('cities', [CityApiController::class, 'index']);
    Route::get('countries/{id}/cities', [CityApiController::class, 'getCitiesByCountry'])->name('area.cities.by-country');

    Route::get('regions', [RegionApiController::class, 'index']);
    Route::get('cities/{id}/regions', [RegionApiController::class, 'getRegionsByCity']);

    Route::get('subregions', [SubRegionApiController::class, 'index']);
    Route::get('regions/{id}/subregions', [SubRegionApiController::class, 'getSubRegionsByRegions']);
});
