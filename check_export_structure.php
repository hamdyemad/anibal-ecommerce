<?php

/**
 * Quick Check: Verify Export Structure
 * 
 * This script exports products and checks if the structure is correct
 * Run: php check_export_structure.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\CatalogManagement\app\Exports\ProductsExport;
use App\Models\User;

echo "\n";
echo "========================================\n";
echo "Export Structure Check\n";
echo "========================================\n\n";

try {
    // Login as admin
    $admin = User::where('user_type_id', 1)->first(); // 1 is typically admin
    
    if (!$admin) {
        // Try alternative method
        $admin = User::whereHas('type', function($q) {
            $q->where('name', 'admin');
        })->first();
    }
    
    if (!$admin) {
        // Just get first user
        $admin = User::first();
    }
    
    if (!$admin) {
        throw new Exception("No users found in database");
    }
    
    Auth::login($admin);
    echo "✅ Logged in as: {$admin->name} (ID: {$admin->id})\n\n";
    
    // Export products
    echo "Exporting products...\n";
    $export = new ProductsExport(true, [], false);
    $fileName = 'structure_check_' . date('YmdHis') . '.xlsx';
    $filePath = 'exports/' . $fileName;
    
    Excel::store($export, $filePath, 'local');
    $fullPath = Storage::disk('local')->path($filePath);
    echo "✅ Export created: {$fullPath}\n\n";
    
    // Load and check structure
    echo "Checking structure...\n";
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
    
    // Check Products Sheet
    echo "\n📋 PRODUCTS SHEET:\n";
    echo "─────────────────────────────────────\n";
    $productsSheet = $spreadsheet->getSheetByName('products');
    if (!$productsSheet) {
        echo "❌ Products sheet not found!\n";
    } else {
        $highestColumn = $productsSheet->getHighestColumn();
        $columnIndex = 'A';
        $headers = [];
        
        while ($columnIndex <= $highestColumn) {
            $value = $productsSheet->getCell($columnIndex . '1')->getValue();
            if ($value) {
                $headers[] = $value;
            }
            $columnIndex++;
        }
        
        echo "Columns found: " . count($headers) . "\n";
        echo "First 10 columns:\n";
        foreach (array_slice($headers, 0, 10) as $i => $header) {
            $col = chr(65 + $i); // A, B, C, etc.
            echo "  {$col}: {$header}\n";
        }
        
        // Check for issues
        echo "\nValidation:\n";
        if ($headers[0] === 'sku') {
            echo "✅ First column is 'sku' (CORRECT)\n";
        } else {
            echo "❌ First column is '{$headers[0]}' (WRONG! Should be 'sku')\n";
        }
        
        if (in_array('id', $headers)) {
            echo "❌ Found 'id' column (WRONG! This should NOT exist)\n";
        } else {
            echo "✅ No 'id' column found (CORRECT)\n";
        }
        
        if (in_array('vendor_id', $headers)) {
            echo "✅ Found 'vendor_id' column (CORRECT for admin)\n";
        } else {
            echo "⚠️  No 'vendor_id' column (Should exist for admin exports)\n";
        }
    }
    
    // Check Images Sheet
    echo "\n📋 IMAGES SHEET:\n";
    echo "─────────────────────────────────────\n";
    $imagesSheet = $spreadsheet->getSheetByName('images');
    if (!$imagesSheet) {
        echo "⚠️  Images sheet not found (may be empty)\n";
    } else {
        $highestColumn = $imagesSheet->getHighestColumn();
        $columnIndex = 'A';
        $headers = [];
        
        while ($columnIndex <= $highestColumn) {
            $value = $imagesSheet->getCell($columnIndex . '1')->getValue();
            if ($value) {
                $headers[] = $value;
            }
            $columnIndex++;
        }
        
        echo "Columns: " . implode(', ', $headers) . "\n";
        
        echo "\nValidation:\n";
        if ($headers[0] === 'sku') {
            echo "✅ First column is 'sku' (CORRECT)\n";
        } else {
            echo "❌ First column is '{$headers[0]}' (WRONG! Should be 'sku')\n";
        }
        
        if (in_array('product_id', $headers)) {
            echo "❌ Found 'product_id' column (WRONG! This should NOT exist)\n";
        } else {
            echo "✅ No 'product_id' column found (CORRECT)\n";
        }
    }
    
    // Check Variants Sheet
    echo "\n📋 VARIANTS SHEET:\n";
    echo "─────────────────────────────────────\n";
    $variantsSheet = $spreadsheet->getSheetByName('variants');
    if (!$variantsSheet) {
        echo "⚠️  Variants sheet not found (may be empty)\n";
    } else {
        $highestColumn = $variantsSheet->getHighestColumn();
        $columnIndex = 'A';
        $headers = [];
        
        while ($columnIndex <= $highestColumn) {
            $value = $variantsSheet->getCell($columnIndex . '1')->getValue();
            if ($value) {
                $headers[] = $value;
            }
            $columnIndex++;
        }
        
        echo "Columns: " . implode(', ', $headers) . "\n";
        
        echo "\nValidation:\n";
        if ($headers[0] === 'product_sku') {
            echo "✅ First column is 'product_sku' (CORRECT)\n";
        } else {
            echo "❌ First column is '{$headers[0]}' (WRONG! Should be 'product_sku')\n";
        }
        
        if (in_array('product_id', $headers)) {
            echo "❌ Found 'product_id' column (WRONG! This should NOT exist)\n";
        } else {
            echo "✅ No 'product_id' column found (CORRECT)\n";
        }
    }
    
    // Final verdict
    echo "\n========================================\n";
    echo "FINAL VERDICT:\n";
    echo "========================================\n";
    
    $productsOk = $productsSheet && $productsSheet->getCell('A1')->getValue() === 'sku' && !in_array('id', $headers);
    $imagesOk = !$imagesSheet || ($imagesSheet->getCell('A1')->getValue() === 'sku');
    $variantsOk = !$variantsSheet || ($variantsSheet->getCell('A1')->getValue() === 'product_sku');
    
    if ($productsOk && $imagesOk && $variantsOk) {
        echo "✅ EXPORT STRUCTURE IS CORRECT!\n";
        echo "\nYou can safely use this export for import.\n";
        echo "The file is saved at: {$fullPath}\n";
        echo "\nNext step: Import this file in the browser.\n";
    } else {
        echo "❌ EXPORT STRUCTURE HAS ISSUES!\n";
        echo "\nProblems found:\n";
        if (!$productsOk) echo "  - Products sheet has wrong structure\n";
        if (!$imagesOk) echo "  - Images sheet has wrong structure\n";
        if (!$variantsOk) echo "  - Variants sheet has wrong structure\n";
        echo "\nThis means the export code is not using the updated classes.\n";
        echo "Check if you've cleared the cache:\n";
        echo "  php artisan cache:clear\n";
        echo "  php artisan config:clear\n";
    }
    
    // Cleanup
    echo "\nCleaning up test file...\n";
    Storage::disk('local')->delete($filePath);
    echo "✅ Done\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
