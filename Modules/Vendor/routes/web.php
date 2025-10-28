<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\app\Http\Controllers\VendorController;



Route::group(
[
	'prefix' => LaravelLocalization::setLocale(),
	'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'setLocaleFromUrl' ]
], function(){

    Route::middleware(['web', 'auth'])->prefix('admin/vendors')->name('admin.vendors.')->group(function() {
        // Vendors
        Route::get('/datatable', [VendorController::class, 'datatable'])->name('datatable');
        Route::resource('/', VendorController::class);

    });
        

});