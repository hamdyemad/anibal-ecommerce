<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\AreaSettings\app\Http\Controllers\CountryController;
use Modules\AreaSettings\app\Http\Controllers\CityController;
use Modules\AreaSettings\app\Http\Controllers\RegionController;
use Modules\AreaSettings\app\Http\Controllers\SubRegionController;

Route::group(
[
	'prefix' => LaravelLocalization::setLocale(),
	'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'setLocaleFromUrl' ]
], function(){

    Route::middleware(['web', 'auth'])->prefix('admin/area-settings')->name('admin.area-settings.')->group(function() {
        
        // Countries
        Route::get('countries/datatable', [CountryController::class, 'datatable'])->name('countries.datatable');
        Route::resource('countries', CountryController::class);
        
        // Cities
        Route::get('cities/datatable', [CityController::class, 'datatable'])->name('cities.datatable');
        Route::get('cities/by-country/{id}', [CityController::class, 'getCitiesByCountry'])->name('cities.by-country');
        Route::resource('cities', CityController::class);
        
        // Regions
        Route::get('regions/datatable', [RegionController::class, 'datatable'])->name('regions.datatable');
        Route::get('regions/by-city/{id}', [RegionController::class, 'getRegionsByCity'])->name('regions.by-city');
        Route::resource('regions', RegionController::class);
        
        // SubRegions
        Route::get('subregions/datatable', [SubRegionController::class, 'datatable'])->name('subregions.datatable');
        Route::get('subregions/by-region/{id}', [SubRegionController::class, 'getSubRegionsByRegion'])->name('subregions.by-region');
        Route::resource('subregions', SubRegionController::class);
    });
});
