<?php

use Illuminate\Support\Facades\Route;
use Modules\CatalogManagement\app\Http\Controllers\VariantsConfigurationController;

// Variant Configuration API Routes (for product form)
Route::prefix('variant-configurations')->group(function () {
    Route::get('key/{keyId}/tree', [VariantsConfigurationController::class, 'getKeyTree']);
    Route::get('{id}', [VariantsConfigurationController::class, 'show']);
});
