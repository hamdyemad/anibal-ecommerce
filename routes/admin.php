<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PaginationController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminManagement\RoleController;
use App\Http\Controllers\AdminManagement\AdminController;
use App\Http\Controllers\AdminManagement\VendorUserController;
use App\Http\Controllers\AreaSettings\CountryController;
use App\Http\Controllers\AreaSettings\CityController;
use App\Http\Controllers\AreaSettings\RegionController;
use App\Http\Controllers\AreaSettings\SubRegionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Api\InjectDataController;
use App\Http\Controllers\Admin\TruncateController;
use Database\Seeders\OrderStageSeeder;
use Database\Seeders\SyncVendorUsersSeeder;
use Database\Seeders\VendorProductTaxSeeder;
use Database\Seeders\VendorSeeder;
use Modules\Customer\app\Models\Customer;
use Modules\Vendor\app\Models\Vendor;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are protected by auth middleware from RouteServiceProvider
|
*/


// Admin dashboard with country code
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Inject data from external source (with lang and country)
Route::get('inject-data', [InjectDataController::class, 'inject'])->name('inject-data');

// Admin Notifications
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
    Route::get('/count', [AdminNotificationController::class, 'count'])->name('count');
    Route::get('/{id}', [AdminNotificationController::class, 'show'])->name('show');
    Route::post('/mark-read', [AdminNotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
});

// Profile Management
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

// Admin Management
Route::prefix('admin-management')->name('admin-management.')->group(function () {
    Route::get('/roles/datatable', [RoleController::class, 'datatable'])->name('roles.data');
    Route::resource('roles', RoleController::class);

    Route::get('/admins/datatable', [AdminController::class, 'datatable'])->name('admins.datatable');
    Route::post('/admins/{admin}/change-status', [AdminController::class, 'changeStatus'])->name('admins.change-status');
    Route::resource('admins', AdminController::class);
});

// Vendor Users Management
Route::prefix('vendor-users-management')->name('vendor-users-management.')->group(function () {
    Route::get('/roles/datatable', [RoleController::class, 'vendorUserRolesDatatable'])->name('roles.data');
    Route::get('/roles/by-vendor', [RoleController::class, 'getRolesByVendor'])->name('roles.by-vendor');
    Route::get('/roles', [RoleController::class, 'vendorUserRolesIndex'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'vendorUserRolesCreate'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'vendorUserRolesStore'])->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'vendorUserRolesShow'])->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'vendorUserRolesEdit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'vendorUserRolesUpdate'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'vendorUserRolesDestroy'])->name('roles.destroy');

    Route::get('/vendor-users/datatable', [VendorUserController::class, 'datatable'])->name('vendor-users.datatable');
    Route::post('/vendor-users/{vendor_user}/change-status', [VendorUserController::class, 'changeStatus'])->name('vendor-users.change-status');
    Route::resource('vendor-users', VendorUserController::class);
});


Route::get('seeder', function () {
    // ===== UPDATE VENDOR ID 4 WITH BNAIA DATA =====
    try {
        echo "🏢 Updating Vendor ID 4 with Bnaia data...\n";

        // Get vendor with ID 4
        $vendor = \Modules\Vendor\app\Models\Vendor::find(246);
        
        if (!$vendor) {
            echo "❌ Vendor with ID 4 not found!\n";
            return response()->json([
                'success' => false,
                'message' => 'Vendor with ID 4 not found',
            ], 404);
        }
        
        echo "  ✓ Found vendor (ID: {$vendor->id}, Name: {$vendor->name})\n";

        // Assign all regions to vendor
        $allRegions = \Modules\AreaSettings\app\Models\Region::pluck('id')->toArray();
        if (!empty($allRegions)) {
            $vendor->regions()->sync($allRegions);
            echo "  ✓ Assigned " . count($allRegions) . " regions to vendor\n";
        }
        
        // Update all vendor_products to use this vendor
        $updatedVendorProducts = \DB::table('vendor_products')
            ->where('vendor_id', '!=', $vendor->id)
            ->update(['vendor_id' => $vendor->id]);
        echo "  ✓ Updated {$updatedVendorProducts} vendor products to vendor ID {$vendor->id}\n";
        
        // Update all order_products to use this vendor
        $updatedOrderProducts = \DB::table('order_products')
            ->where('vendor_id', '!=', $vendor->id)
            ->update(['vendor_id' => $vendor->id]);
        echo "  ✓ Updated {$updatedOrderProducts} order products to vendor ID {$vendor->id}\n";
        
        // Update all vendor_order_stages to use this vendor
        $updatedVendorStages = \DB::table('vendor_order_stages')
            ->where('vendor_id', '!=', $vendor->id)
            ->update(['vendor_id' => $vendor->id]);
        echo "  ✓ Updated {$updatedVendorStages} vendor order stages to vendor ID {$vendor->id}\n";
        
        echo "✅ Vendor ID 4 update complete!\n\n";
        
        return response()->json([
            'success' => true,
            'message' => 'Vendor ID 4 updated successfully with Bnaia data',
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->name,
            'updates' => [
                'vendor_products' => $updatedVendorProducts,
                'order_products' => $updatedOrderProducts,
                'vendor_order_stages' => $updatedVendorStages,
                'regions' => count($allRegions),
            ],
        ]);
        
    } catch (\Exception $e) {
        echo "❌ Error updating vendor: {$e->getMessage()}\n\n";
        return response()->json([
            'success' => false,
            'message' => 'Error updating vendor',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

Route::get('/truncate', [TruncateController::class, 'truncate'])->name('truncate');
