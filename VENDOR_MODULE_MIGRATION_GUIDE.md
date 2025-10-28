# Vendor Module Migration Guide

## Overview
This guide helps you move all vendor-related files from the main app to the Vendor module.

## Step 1: Run the PowerShell Script
```powershell
cd C:\laragon\www\hexa
.\move_vendors_to_module.ps1
```

## Step 2: Manual Namespace Updates

### 2.1 Update VendorRequest.php
File: `Modules/Vendor/app/Http/Requests/VendorRequest.php`

Change:
```php
namespace App\Http\Requests\Vendor;
use App\Models\Vendor;
```

To:
```php
namespace Modules\Vendor\app\Http\Requests;
use Modules\Vendor\app\Models\Vendor;
```

### 2.2 Update VendorService.php
File: `Modules/Vendor/app/Services/VendorService.php`

Change:
```php
namespace App\Services;
use App\Interfaces\VendorInterface;
use App\Models\Vendor;
```

To:
```php
namespace Modules\Vendor\app\Services;
use Modules\Vendor\app\Interfaces\VendorInterface;
use Modules\Vendor\app\Models\Vendor;
```

### 2.3 Update VendorRepository.php
File: `Modules/Vendor/app/Repositories/VendorRepository.php`

Change:
```php
namespace App\Repositories;
use App\Interfaces\VendorInterface;
use App\Models\Vendor;
```

To:
```php
namespace Modules\Vendor\app\Repositories;
use Modules\Vendor\app\Interfaces\VendorInterface;
use Modules\Vendor\app\Models\Vendor;
```

### 2.4 Update VendorInterface.php
File: `Modules/Vendor/app/Interfaces/VendorInterface.php`

Change:
```php
namespace App\Interfaces;
```

To:
```php
namespace Modules\Vendor\app\Interfaces;
```

### 2.5 Update View Paths in VendorController.php
File: `Modules/Vendor/app/Http/Controllers/VendorController.php`

Replace all view paths:
- `pages.vendors.index` → `vendor::vendor.index`
- `pages.vendors.form` → `vendor::vendor.form`
- `pages.vendors.show` → `vendor::vendor.show`

Example:
```php
// Before
return view('pages.vendors.index', compact('languages'));

// After
return view('vendor::vendor.index', compact('languages'));
```

## Step 3: Create VendorServiceProvider

Create file: `Modules/Vendor/app/Providers/VendorServiceProvider.php`

```php
<?php

namespace Modules\Vendor\app\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Vendor\app\Interfaces\VendorInterface;
use Modules\Vendor\app\Repositories\VendorRepository;

class VendorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(VendorInterface::class, VendorRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        
        // Register views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'vendor');
        
        // Register translations
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'vendor');
    }
}
```

## Step 4: Register VendorServiceProvider

Edit: `Modules/Vendor/config/config.php`

Add:
```php
'providers' => [
    Modules\Vendor\app\Providers\VendorServiceProvider::class
],
```

## Step 5: Update Routes

Edit: `Modules/Vendor/routes/web.php`

Replace content with:
```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\app\Http\Controllers\VendorController;

Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('datatable', [VendorController::class, 'datatable'])->name('datatable');
        Route::resource('/', VendorController::class)->parameters(['' => 'vendor']);
    });
});
```

## Step 6: Copy Language Files

### English (lang/en/vendor.php)
```powershell
Copy-Item -Path "lang\en\vendor.php" -Destination "Modules\Vendor\lang\en\vendor.php"
```

### Arabic (lang/ar/vendor.php)
```powershell
Copy-Item -Path "lang\ar\vendor.php" -Destination "Modules\Vendor\lang\ar\vendor.php"
```

## Step 7: Remove Old Route

Edit: `routes/admin.php`

Remove the vendor routes:
```php
// Remove these lines:
Route::get('vendors/datatable', [VendorController::class, 'datatable'])->name('vendors.datatable');
Route::resource('vendors', VendorController::class);
```

## Step 8: Update AppServiceProvider

Edit: `app/Providers/AppServiceProvider.php`

Remove vendor interface binding (if exists):
```php
// Remove this:
$this->app->bind(VendorInterface::class, VendorRepository::class);
```

## Step 9: Run Composer Autoload

```bash
composer dump-autoload
```

## Step 10: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Step 11: Test

1. Visit: `http://your-site.test/admin/vendors`
2. Check if the list loads correctly
3. Try creating a new vendor
4. Try editing a vendor
5. Try deleting a vendor

## Troubleshooting

### Issue: Class not found
**Solution**: Run `composer dump-autoload`

### Issue: View not found
**Solution**: Check view paths use `vendor::vendor.index` format

### Issue: Route not found
**Solution**: Clear route cache with `php artisan route:clear`

### Issue: Interface binding error
**Solution**: Make sure VendorServiceProvider is registered in config

## File Structure After Migration

```
Modules/Vendor/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── VendorController.php
│   │   └── Requests/
│   │       └── VendorRequest.php
│   ├── Models/
│   │   ├── Vendor.php
│   │   └── VendorCommission.php
│   ├── Services/
│   │   └── VendorService.php
│   ├── Repositories/
│   │   └── VendorRepository.php
│   ├── Interfaces/
│   │   └── VendorInterface.php
│   └── Providers/
│       └── VendorServiceProvider.php
├── database/
│   └── migrations/
│       ├── 2025_10_23_110554_create_vendors_table.php
│       ├── 2025_10_23_110555_create_vendor_commission_table.php
│       └── 2025_10_23_132038_create_vendors_activities_table.php
├── resources/
│   └── views/
│       └── vendor/
│           ├── index.blade.php
│           ├── form.blade.php
│           └── show.blade.php
├── lang/
│   ├── en/
│   │   └── vendor.php
│   └── ar/
│       └── vendor.php
└── routes/
    └── web.php
```

## Completion Checklist

- [ ] Run PowerShell script
- [ ] Update all namespaces
- [ ] Create VendorServiceProvider
- [ ] Register VendorServiceProvider
- [ ] Update routes in module
- [ ] Copy language files
- [ ] Remove old routes from admin.php
- [ ] Update AppServiceProvider
- [ ] Run composer dump-autoload
- [ ] Clear all caches
- [ ] Test all vendor operations
- [ ] Remove old files after verification

## Final Cleanup (After Testing Successfully)

```powershell
# Remove old directories (only after testing!)
Remove-Item -Path "app\Http\Requests\Vendor" -Recurse -Force
Remove-Item -Path "resources\views\pages\vendors" -Recurse -Force
```

---
**Note**: Keep backups before deleting old files!
