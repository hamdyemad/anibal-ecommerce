<?php

namespace Modules\CategoryManagment\app\Traits;

trait HandlesSortNumber
{
    /**
     * Handle sort number to prevent duplicates globally
     * 
     * @param string $modelClass The model class (e.g., Category::class, Department::class)
     * @param int|null $itemId The item being updated (null for new items)
     * @param int $newSortNumber The desired sort number
     * @param int|null $oldSortNumber The current sort number (null for new items)
     * @param array $additionalConditions Additional where conditions (e.g., ['department_id' => 1])
     * @return void
     */
    protected function handleSortNumber(
        string $modelClass,
        ?int $itemId,
        int $newSortNumber,
        ?int $oldSortNumber = null,
        array $additionalConditions = []
    ) {
        // Build base query
        $query = $modelClass::where('sort_number', $newSortNumber);
        
        // Exclude current item if updating
        if ($itemId) {
            $query->where('id', '!=', $itemId);
        }
        
        // Apply additional conditions (e.g., same department)
        foreach ($additionalConditions as $column => $value) {
            $query->where($column, $value);
        }
        
        // Check if another item already has this sort number
        $existingItem = $query->first();
        
        // If there's a duplicate, we need to shift
        if ($existingItem) {
            if ($oldSortNumber === null || $newSortNumber === $oldSortNumber) {
                // New item OR same sort number but there's a duplicate - shift duplicates down
                $shiftQuery = $modelClass::where('sort_number', '>=', $newSortNumber);
                
                if ($itemId) {
                    $shiftQuery->where('id', '!=', $itemId);
                }
                
                // Apply additional conditions
                foreach ($additionalConditions as $column => $value) {
                    $shiftQuery->where($column, $value);
                }
                
                $shiftQuery->increment('sort_number');
            } else {
                // Different sort number - do normal shifting
                if ($newSortNumber < $oldSortNumber) {
                    // Moving up: shift items down between new and old position
                    $shiftQuery = $modelClass::where('id', '!=', $itemId)
                        ->where('sort_number', '>=', $newSortNumber)
                        ->where('sort_number', '<', $oldSortNumber);
                    
                    // Apply additional conditions
                    foreach ($additionalConditions as $column => $value) {
                        $shiftQuery->where($column, $value);
                    }
                    
                    $shiftQuery->increment('sort_number');
                } else {
                    // Moving down: shift items up between old and new position
                    $shiftQuery = $modelClass::where('id', '!=', $itemId)
                        ->where('sort_number', '>', $oldSortNumber)
                        ->where('sort_number', '<=', $newSortNumber);
                    
                    // Apply additional conditions
                    foreach ($additionalConditions as $column => $value) {
                        $shiftQuery->where($column, $value);
                    }
                    
                    $shiftQuery->decrement('sort_number');
                }
            }
        }
    }

    /**
     * Handle sort number cleanup after deletion
     * Shifts down all items with higher sort numbers to fill the gap
     * 
     * @param string $modelClass The model class
     * @param int $deletedSortNumber The sort number of the deleted item
     * @param array $additionalConditions Additional where conditions
     * @return void
     */
    protected function handleSortNumberAfterDelete(
        string $modelClass,
        int $deletedSortNumber,
        array $additionalConditions = []
    ) {
        // Shift down all items with higher sort numbers to fill the gap
        $query = $modelClass::where('sort_number', '>', $deletedSortNumber);
        
        // Apply additional conditions
        foreach ($additionalConditions as $column => $value) {
            $query->where($column, $value);
        }
        
        $query->decrement('sort_number');
    }
}
