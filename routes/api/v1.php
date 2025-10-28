<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API V1 routes for your application.
| Base URL: /api/v1
|
*/

/*
|--------------------------------------------------------------------------
| Area Settings API Routes
|--------------------------------------------------------------------------
| Base URL: /api/v1/area
*/
Route::prefix('area')->group(function () {
    // Country Routes - /api/v1/area/countries
    require __DIR__ . '/v1/area/country.php';
});
