<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration:
     * 1. Deletes all ads associated with "Main Category" positions
     * 2. Deletes all ad positions that contain "Main Category" in their name
     * 3. Deletes "Middle Home Ad" position with 300x300 dimensions
     * 4. Creates a new clean "Main Category" position
     */
    public function up(): void
    {
        // Step 1: Get all position IDs that contain "Main Category"
        $positionIds = DB::table('ads_positions')
            ->where('position', 'LIKE', '%Main Category%')
            ->pluck('id')
            ->toArray();

        // Step 2: Get "Middle Home Ad" with 300x300 dimensions
        $middleHomeAdIds = DB::table('ads_positions')
            ->where('position', 'LIKE', '%Middle Home Ad%')
            ->where('width', 300)
            ->where('height', 300)
            ->pluck('id')
            ->toArray();

        // Merge all position IDs to delete
        $allPositionIds = array_merge($positionIds, $middleHomeAdIds);

        if (!empty($allPositionIds)) {
            // Step 3: Delete all ads associated with these positions
            DB::table('ads')
                ->whereIn('ad_position_id', $allPositionIds)
                ->delete();

            // Step 4: Delete "Main Category" positions
            DB::table('ads_positions')
                ->where('position', 'LIKE', '%Main Category%')
                ->delete();

            // Step 5: Delete "Middle Home Ad" with 300x300
            DB::table('ads_positions')
                ->where('position', 'LIKE', '%Middle Home Ad%')
                ->where('width', 300)
                ->where('height', 300)
                ->delete();
        }

        // Step 6: Create new clean "Main Category" position
        DB::table('ads_positions')->insert([
            'position' => 'Sidebar Ad',
            'width' => 300,
            'height' => 800,
            'device' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the clean "Main Category" position created by this migration
        DB::table('ads_positions')
            ->where('position', '=', 'Main Category')
            ->where('width', 300)
            ->where('height', 800)
            ->delete();
    }
};
