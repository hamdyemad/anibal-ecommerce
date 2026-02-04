<?php

/**
 * Script to clean up "Main Category" ad positions
 * 
 * This script will:
 * 1. Delete all ad positions that contain "Main Category" in their name
 * 2. Delete any ads associated with those positions
 * 3. Create a new clean "Main Category" position
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\SystemSetting\app\Models\AdPosition;
use Modules\SystemSetting\app\Models\Ad;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    
    echo "Starting cleanup of Main Category positions...\n\n";
    
    // Step 1: Find all positions that contain "Main Category"
    $positionsToDelete = AdPosition::where('position', 'LIKE', '%Main Category%')
        ->orWhere('name', 'LIKE', '%Main Category%')
        ->get();
    
    if ($positionsToDelete->isEmpty()) {
        echo "No positions found containing 'Main Category'\n";
    } else {
        echo "Found " . $positionsToDelete->count() . " position(s) containing 'Main Category':\n";
        foreach ($positionsToDelete as $position) {
            echo "  - ID: {$position->id}, Position: {$position->position}\n";
        }
        echo "\n";
        
        // Step 2: Delete ads associated with these positions
        $positionIds = $positionsToDelete->pluck('id')->toArray();
        $adsCount = Ad::whereIn('ad_position_id', $positionIds)->count();
        
        if ($adsCount > 0) {
            echo "Deleting {$adsCount} ad(s) associated with these positions...\n";
            Ad::whereIn('ad_position_id', $positionIds)->delete();
            echo "✓ Ads deleted\n\n";
        }
        
        // Step 3: Delete the positions
        echo "Deleting " . $positionsToDelete->count() . " position(s)...\n";
        AdPosition::where('position', 'LIKE', '%Main Category%')
            ->orWhere('name', 'LIKE', '%Main Category%')
            ->delete();
        echo "✓ Positions deleted\n\n";
    }
    
    // Step 4: Create new clean "Main Category" position
    echo "Creating new 'Main Category' position...\n";
    
    $newPosition = AdPosition::create([
        'position' => 'Main Category',
        'name' => 'Main Category',
        'width' => 1200,
        'height' => 400,
        'device' => 'web',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✓ New position created:\n";
    echo "  - ID: {$newPosition->id}\n";
    echo "  - Position: {$newPosition->position}\n";
    echo "  - Dimensions: {$newPosition->width}x{$newPosition->height}\n";
    echo "  - Device: {$newPosition->device}\n\n";
    
    DB::commit();
    
    echo "✅ Cleanup completed successfully!\n";
    echo "\nSummary:\n";
    echo "  - Deleted positions: " . $positionsToDelete->count() . "\n";
    echo "  - Deleted ads: " . ($adsCount ?? 0) . "\n";
    echo "  - Created new position: Main Category (ID: {$newPosition->id})\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
