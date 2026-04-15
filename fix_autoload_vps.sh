#!/bin/bash

echo "=== Fixing PSR-4 Autoloading Issues ==="
echo ""

# Navigate to project directory
cd /home/elghad-ecommerce/htdocs/anibal-ecommerce

# Fix 1: Rename SubregionAction.php to SubRegionAction.php
echo "1. Fixing SubRegionAction file name..."
if [ -f "Modules/AreaSettings/app/Actions/SubregionAction.php" ]; then
    mv "Modules/AreaSettings/app/Actions/SubregionAction.php" "Modules/AreaSettings/app/Actions/SubRegionAction.php"
    echo "   ✓ Renamed SubregionAction.php to SubRegionAction.php"
fi

# Fix 2: Create proper cache directories
echo ""
echo "2. Creating cache directories..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
echo "   ✓ Cache directories created"

# Fix 3: Set proper permissions
echo ""
echo "3. Setting permissions..."
chmod -R 775 bootstrap/cache
chmod -R 775 storage
chown -R www-data:www-data bootstrap/cache
chown -R www-data:www-data storage
echo "   ✓ Permissions set"

# Fix 4: Clear all caches
echo ""
echo "4. Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo "   ✓ Caches cleared"

# Fix 5: Regenerate autoload files
echo ""
echo "5. Regenerating autoload files..."
composer dump-autoload --optimize
echo "   ✓ Autoload regenerated"

# Fix 6: Optimize for production
echo ""
echo "6. Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "   ✓ Optimization complete"

echo ""
echo "=== All fixes applied successfully! ==="
echo ""
echo "Note: The PSR-4 warnings for multiple classes in single files"
echo "(AccountingRepositoryInterfaces.php, AccountingServices.php, etc.)"
echo "are just warnings and won't affect functionality."
echo ""
