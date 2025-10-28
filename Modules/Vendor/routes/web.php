<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\app\Http\Controllers\VendorController;



Route::group(
[
	'prefix' => 'admin',
    'as' => 'admin.'
], function(){
    // Vendors
    Route::get('vendors/datatable', [VendorController::class, 'datatable'])->name('vendors.datatable');
    Route::resource('vendors', VendorController::class);
});