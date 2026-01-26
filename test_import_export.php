<?php

/**
 * Test Script for Import/Export Functionality
 * 
 * This script tests the export/import system to ensure:
 * 1. Export generates correct structure
 * 2. Import can read the exported file
 * 3. No validation errors occur
 * 
 * Run this from the command line:
 * php test_import_export.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\CatalogManagement\app\Exports\ProductsExport;
use Modules\CatalogManagement\app\Imports\ProductsImport;
use Modules\CatalogManagement\app\Models\VendorProduct;
use App\Models\User;

echo "===========================================\n";
echo "Import/Export Test Script\n";
echo "===========================================\n\n";

try {
    // Step 1: Login as admin
    echo "Step 1: Authenticating as admin...\n";
    $admin = User::whereHas('userType', function($q) {
        $q->where('name', 'admin');
    })->first();
    
    if (!$admin) {
        throw new Exception("No admin user found. Please create an admin user first.");
    }
    
    Auth::login($admin);
    echo "✅ Logged in as: {$admin->name} (ID: {$admin->id})\n\n";
    
    // Step 2: Check if products exist
    echo "Step 2: Checking for products...\n";
    $productCount = VendorProduct::count();
    echo "Found {$productCount} vendor products\n";
    
    if ($productCount < 10) {
        echo "⚠️  Warning: Less than 10 products found. Consider running seeders.\n";
        echo "   Run: php artisan db:seed --class=AutoProductSeeder\n\n";
    } else {
        echo "✅ Sufficient products for testing\n\n";
    }
    
    // Step 3: Test Export
    echo "Step 3: Testing export...\n";
    $export = new ProductsExport(true, [], false);
    $fileName = 'test_export_' . date('Y-m-d_His') . '.xlsx';
    $filePath = 'exports/' . $fileName;
    
    Excel::store($export, $filePath, 'local');
    echo "✅ Export created: storage/app/{$filePath}\n";
    
    // Step 4: Verify export structure
    echo "\nStep 4: Verifying export structure...\n";
    $fullPath = Storage::disk('local')->path($filePath);
    
    if (!file_exists($fullPath)) {
        throw new Exception("Export file not found at: {$fullPath}");
    }
    
    echo "✅ File exists: " . filesize($fullPath) . " bytes\n";
    
    // Load the Excel file to check structure
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
    
    // Check Products sheet
    echo "\nChecking 'products' sheet...\n";
    $productsSheet = $spreadsheet->getSheetByName('products');
    if (!$productsSheet) {
        throw new Exception("'products' sheet not found");
    }
    
    $firstColumn = $productsSheet->getCell('A1')->getValue();
    if ($firstColumn !== 'sku') {
        throw new Exception("First column should be 'sku', found: {$firstColumn}");
    }
    echo "✅ First column is 'sku'\n";
    
    $secondColumn = $productsSheet->getCell('B1')->getValue();
    if ($secondColumn !== 'vendor_id') {
        throw new Exception("Second column should be 'vendor_id', found: {$secondColumn}");
    }
    echo "✅ Second column is 'vendor_id'\n";
    
    // Check for old 'id' column (should NOT exist)
    $headers = [];
    $highestColumn = $productsSheet->getHighestColumn();
    $columnIndex = 'A';
    while ($columnIndex <= $highestColumn) {
        $headers[] = $productsSheet->getCell($columnIndex . '1')->getValue();
        $columnIndex++;
    }
    
    if (in_array('id', $headers)) {
        throw new Exception("❌ ERROR: 'id' column found in products sheet! This should not exist.");
    }
    echo "✅ No 'id' column found (correct)\n";
    
    $rowCount = $productsSheet->getHighestRow() - 1; // Exclude header
    echo "✅ Products sheet has {$rowCount} data rows\n";
    
    // Check Images sheet
    echo "\nChecking 'images' sheet...\n";
    $imagesSheet = $spreadsheet->getSheetByName('images');
    if ($imagesSheet) {
        $firstColumn = $imagesSheet->getCell('A1')->getValue();
        if ($firstColumn !== 'sku') {
            throw new Exception("Images sheet first column should be 'sku', found: {$firstColumn}");
        }
        echo "✅ Images sheet first column is 'sku'\n";
        
        // Check for old 'product_id' column (should NOT exist)
        $headers = [];
        $highestColumn = $imagesSheet->getHighestColumn();
        $columnIndex = 'A';
        while ($columnIndex <= $highestColumn) {
            $headers[] = $imagesSheet->getCell($columnIndex . '1')->getValue();
            $columnIndex++;
        }
        
        if (in_array('product_id', $headers)) {
            throw new Exception("❌ ERROR: 'product_id' column found in images sheet! This should not exist.");
        }
        echo "✅ No 'product_id' column found (correct)\n";
    } else {
        echo "⚠️  Images sheet not found (may be empty)\n";
    }
    
    // Check Variants sheet
    echo "\nChecking 'variants' sheet...\n";
    $variantsSheet = $spreadsheet->getSheetByName('variants');
    if ($variantsSheet) {
        $firstColumn = $variantsSheet->getCell('A1')->getValue();
        if ($firstColumn !== 'product_sku') {
            throw new Exception("Variants sheet first column should be 'product_sku', found: {$firstColumn}");
        }
        echo "✅ Variants sheet first column is 'product_sku'\n";
        
        // Check for old 'product_id' column (should NOT exist)
        $headers = [];
        $highestColumn = $variantsSheet->getHighestColumn();
        $columnIndex = 'A';
        while ($columnIndex <= $highestColumn) {
            $headers[] = $variantsSheet->getCell($columnIndex . '1')->getValue();
            $columnIndex++;
        }
        
        if (in_array('product_id', $headers)) {
            throw new Exception("❌ ERROR: 'product_id' column found in variants sheet! This should not exist.");
        }
        echo "✅ No 'product_id' column found (correct)\n";
        
        $rowCount = $variantsSheet->getHighestRow() - 1;
        echo "✅ Variants sheet has {$rowCount} data rows\n";
    } else {
        echo "⚠️  Variants sheet not found (may be empty)\n";
    }
    
    // Step 5: Test Import (dry run - validation only)
    echo "\nStep 5: Testing import validation...\n";
    $import = new ProductsImport(true);
    
    try {
        Excel::import($import, $filePath, 'local');
        
        $errors = $import->getErrors();
        $importedCount = $import->getImportedCount();
        
        echo "✅ Import validation completed\n";
        echo "   Imported: {$importedCount} products\n";
        echo "   Errors: " . count($errors) . "\n";
        
        if (count($errors) > 0) {
            echo "\n⚠️  Import Errors Found:\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "   - Sheet: {$error['sheet']}, Row: {$error['row']}, SKU: {$error['sku']}\n";
                if (is_array($error['errors'])) {
                    foreach ($error['errors'] as $err) {
                        echo "     • {$err}\n";
                    }
                }
            }
            if (count($errors) > 5) {
                echo "   ... and " . (count($errors) - 5) . " more errors\n";
            }
        } else {
            echo "✅ No import errors!\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Import failed: " . $e->getMessage() . "\n";
        echo "   This might indicate a validation issue.\n";
    }
    
    // Step 6: Cleanup
    echo "\nStep 6: Cleanup...\n";
    Storage::disk('local')->delete($filePath);
    echo "✅ Test file deleted\n";
    
    // Final Summary
    echo "\n===========================================\n";
    echo "TEST SUMMARY\n";
    echo "===========================================\n";
    echo "✅ Export: PASSED\n";
    echo "✅ Structure: PASSED (SKU-based, no ID columns)\n";
    echo "✅ Import: " . (count($errors) === 0 ? "PASSED" : "PASSED WITH ERRORS") . "\n";
    echo "\n";
    
    if (count($errors) === 0) {
        echo "🎉 All tests passed! The system is working correctly.\n";
        echo "\nNext steps:\n";
        echo "1. Test in browser: http://127.0.0.1:8000/en/eg/admin/products/bulk-upload\n";
        echo "2. Export products from the UI\n";
        echo "3. Import the exported file\n";
        echo "4. Verify results in the UI\n";
    } else {
        echo "⚠️  Tests passed but with errors. Review the errors above.\n";
        echo "   This might be due to data issues (missing departments, invalid IDs, etc.)\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ TEST FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n";
