<?php

/**
 * Test Import Synchronously (No Batch Jobs)
 * 
 * This script imports an Excel file directly without using batch jobs
 * so you can see errors immediately.
 * 
 * Usage: php test_import_sync.php path/to/your/file.xlsx
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Modules\CatalogManagement\app\Imports\ProductsImport;
use App\Models\User;

// Check if file path is provided
if ($argc < 2) {
    echo "Usage: php test_import_sync.php path/to/your/file.xlsx\n";
    echo "Example: php test_import_sync.php storage/app/products_export_2026-01-26_150704.xlsx\n";
    exit(1);
}

$filePath = $argv[1];

echo "\n";
echo "========================================\n";
echo "Synchronous Import Test\n";
echo "========================================\n\n";

try {
    // Check if file exists
    if (!file_exists($filePath)) {
        throw new Exception("File not found: {$filePath}");
    }
    
    echo "File: {$filePath}\n";
    echo "Size: " . filesize($filePath) . " bytes\n\n";
    
    // Login as admin
    $admin = User::where('user_type_id', 1)->first();
    if (!$admin) {
        $admin = User::first();
    }
    
    if (!$admin) {
        throw new Exception("No users found in database");
    }
    
    Auth::login($admin);
    echo "✅ Logged in as: {$admin->name} (ID: {$admin->id})\n\n";
    
    // Create import instance
    echo "Starting import...\n";
    $import = new ProductsImport(true); // true = isAdmin
    
    // Import the file
    Excel::import($import, $filePath);
    
    // Get results
    $errors = $import->getErrors();
    $importedCount = $import->getImportedCount();
    
    echo "\n========================================\n";
    echo "IMPORT RESULTS\n";
    echo "========================================\n\n";
    
    echo "✅ Imported: {$importedCount} products\n";
    echo "❌ Errors: " . count($errors) . "\n\n";
    
    if (count($errors) > 0) {
        echo "ERROR DETAILS:\n";
        echo "─────────────────────────────────────\n\n";
        
        // Group errors by sheet
        $errorsBySheet = [];
        foreach ($errors as $error) {
            $sheet = $error['sheet'] ?? 'unknown';
            if (!isset($errorsBySheet[$sheet])) {
                $errorsBySheet[$sheet] = [];
            }
            $errorsBySheet[$sheet][] = $error;
        }
        
        foreach ($errorsBySheet as $sheet => $sheetErrors) {
            echo "📋 {$sheet} Sheet: " . count($sheetErrors) . " errors\n";
            echo "─────────────────────────────────────\n";
            
            // Show first 5 errors for this sheet
            foreach (array_slice($sheetErrors, 0, 5) as $error) {
                echo "  Row {$error['row']}: ";
                echo "SKU: " . ($error['sku'] ?? $error['id'] ?? 'N/A') . "\n";
                
                if (is_array($error['errors'])) {
                    foreach ($error['errors'] as $err) {
                        echo "    • {$err}\n";
                    }
                } else {
                    echo "    • {$error['errors']}\n";
                }
                echo "\n";
            }
            
            if (count($sheetErrors) > 5) {
                echo "  ... and " . (count($sheetErrors) - 5) . " more errors\n";
            }
            echo "\n";
        }
    }
    
    echo "========================================\n";
    
    if (count($errors) === 0) {
        echo "🎉 SUCCESS! All products imported without errors!\n";
    } else {
        echo "⚠️  Import completed with errors. See details above.\n";
    }
    
    echo "\n";
    
} catch (\Exception $e) {
    echo "\n❌ IMPORT FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nStack Trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
