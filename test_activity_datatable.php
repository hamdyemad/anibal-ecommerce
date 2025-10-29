<?php

/**
 * Quick test script to verify activity datatable functionality
 * Run from project root: php test_activity_datatable.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\CategoryManagment\app\Models\Activity;
use App\Models\Language;

echo "=== Activity Datatable Test ===\n\n";

// Test 1: Check if activities exist
echo "1. Checking activities in database...\n";
$activitiesCount = Activity::count();
echo "   Total activities: {$activitiesCount}\n\n";

if ($activitiesCount > 0) {
    // Test 2: Show sample activities
    echo "2. Sample activities:\n";
    $activities = Activity::with('translations')->take(5)->get();
    foreach ($activities as $activity) {
        echo "   - ID: {$activity->id}, Active: {$activity->active}, Translations: {$activity->translations->count()}\n";
        foreach ($activity->translations as $translation) {
            echo "     * {$translation->lang_key}: {$translation->lang_value} (lang_id: {$translation->lang_id})\n";
        }
    }
    echo "\n";
} else {
    echo "   ⚠ No activities found in database!\n\n";
}

// Test 3: Check languages
echo "3. Checking languages...\n";
$languages = Language::all();
echo "   Total languages: {$languages->count()}\n";
foreach ($languages as $lang) {
    echo "   - {$lang->name} (ID: {$lang->id}, Code: {$lang->code})\n";
}
echo "\n";

// Test 4: Test the repository query
echo "4. Testing ActivityRepository query...\n";
try {
    $repo = app(\Modules\CategoryManagment\app\Interfaces\ActivityRepositoryInterface::class);
    $query = $repo->getActivitiesQuery([]);
    $count = $query->count();
    echo "   Activities from repository query: {$count}\n";
    
    if ($count > 0) {
        echo "   ✓ Repository is working correctly!\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
