<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InjectDataController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "api" middleware group. Enjoy building your API!
|
*/

// Temporary route for testing inject data
Route::get('inject-data', [InjectDataController::class, 'inject']);
