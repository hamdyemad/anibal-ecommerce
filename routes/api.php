<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Ultra-fast test endpoint with minimal overhead
Route::get('/test', function () {
    return 'test';
});
