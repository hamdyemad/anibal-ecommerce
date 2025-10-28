# PowerShell script to move all Vendor-related files to Vendor module
Write-Host "Starting Vendor module migration..." -ForegroundColor Green

# Base paths
$basePath = "C:\laragon\www\hexa"
$modulePath = "$basePath\Modules\Vendor"

# Create necessary directories
Write-Host "`nCreating directories..." -ForegroundColor Yellow
$directories = @(
    "$modulePath\app\Models",
    "$modulePath\app\Http\Controllers",
    "$modulePath\app\Http\Requests",
    "$modulePath\app\Services",
    "$modulePath\app\Repositories",
    "$modulePath\app\Interfaces",
    "$modulePath\database\migrations",
    "$modulePath\lang\en",
    "$modulePath\lang\ar",
    "$modulePath\resources\views\vendor"
)

foreach ($dir in $directories) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Host "Created: $dir" -ForegroundColor Cyan
    }
}

# Move files
Write-Host "`nMoving files..." -ForegroundColor Yellow

# Move Controller (already updated)
Write-Host "Moving Controller..." -ForegroundColor Cyan
Move-Item -Path "$basePath\app\Http\Controllers\VendorController.php" `
    -Destination "$modulePath\app\Http\Controllers\VendorController.php" -Force

# Copy and remove Models (already created in module)
Write-Host "Removing old Models..." -ForegroundColor Cyan
Remove-Item -Path "$basePath\app\Models\Vendor.php" -Force
Remove-Item -Path "$basePath\app\Models\VendorCommission.php" -Force

# Move Requests
Write-Host "Moving Requests..." -ForegroundColor Cyan
Move-Item -Path "$basePath\app\Http\Requests\Vendor\VendorRequest.php" `
    -Destination "$modulePath\app\Http\Requests\VendorRequest.php" -Force

# Move Services
Write-Host "Moving Services..." -ForegroundColor Cyan
Move-Item -Path "$basePath\app\Services\VendorService.php" `
    -Destination "$modulePath\app\Services\VendorService.php" -Force

# Move Repositories
Write-Host "Moving Repositories..." -ForegroundColor Cyan
Move-Item -Path "$basePath\app\Repositories\VendorRepository.php" `
    -Destination "$modulePath\app\Repositories\VendorRepository.php" -Force

# Move Interfaces
Write-Host "Moving Interfaces..." -ForegroundColor Cyan
Move-Item -Path "$basePath\app\Interfaces\VendorInterface.php" `
    -Destination "$modulePath\app\Interfaces\VendorInterface.php" -Force

# Move Views
Write-Host "Moving Views..." -ForegroundColor Cyan
Copy-Item -Path "$basePath\resources\views\pages\vendors\*" `
    -Destination "$modulePath\resources\views\vendor\" -Recurse -Force

# Move Migrations
Write-Host "Moving Migrations..." -ForegroundColor Cyan
Move-Item -Path "$basePath\database\migrations\2025_10_23_110554_create_vendors_table.php" `
    -Destination "$modulePath\database\migrations\" -Force
Move-Item -Path "$basePath\database\migrations\2025_10_23_110555_create_vendor_commission_table.php" `
    -Destination "$modulePath\database\migrations\" -Force
Move-Item -Path "$basePath\database\migrations\2025_10_23_132038_create_vendors_activities_table.php" `
    -Destination "$modulePath\database\migrations\" -Force

Write-Host "`n✅ File migration completed!" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "1. Update namespace in VendorRequest.php"
Write-Host "2. Update namespace in VendorService.php"
Write-Host "3. Update namespace in VendorRepository.php"
Write-Host "4. Update namespace in VendorInterface.php"
Write-Host "5. Update view paths in VendorController.php"
Write-Host "6. Create VendorServiceProvider.php"
Write-Host "7. Copy language files"
Write-Host "8. Update routes/web.php"
Write-Host "9. Register module in config/modules.php"
Write-Host "10. Run: composer dump-autoload"
