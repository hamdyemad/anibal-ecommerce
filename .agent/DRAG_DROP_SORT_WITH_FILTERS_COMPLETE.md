# Drag & Drop Sorting with Search/Filter - Implementation Complete

## Status: ✅ COMPLETE

## Problem
When searching or filtering tables, drag & drop sorting was using visual positions (1, 2, 3...) instead of actual database sort_numbers, causing incorrect swaps.

## Solution Implemented

### 1. Data Attributes Added
All drag handles now include `data-sort-number` attribute with the actual database value:
```blade
<span class="drag-handle" data-id="${row.id}" data-sort-number="${row.sort_number || 0}">
```

### 2. Sort Number Detection Logic
The sortable `update` handler now:
- Reads `data-sort-number` from the dragged row
- Checks the next row's `data-sort-number` to determine target position
- Falls back to previous row if no next row exists
- Uses actual database values, not visual positions

### 3. Swap Logic
Backend controllers swap sort_numbers between:
- Dragged item's old sort_number
- Target item's sort_number

### 4. Entities Updated
✅ **Products** (CatalogManagement)
- File: `Modules/CatalogManagement/resources/views/product/product_configurations_table/_custom-handlers.blade.php`
- Uses datatable-wrapper component with `data-sort-number` attributes

✅ **Departments** (CategoryManagement)
- File: `Modules/CategoryManagment/resources/views/department/index.blade.php`
- Custom sortable implementation with sort_number detection

✅ **Categories** (CategoryManagement)
- File: `Modules/CategoryManagment/resources/views/category/index.blade.php`
- Custom sortable implementation with sort_number detection

✅ **SubCategories** (CategoryManagement)
- File: `Modules/CategoryManagment/resources/views/subcategory/index.blade.php`
- Custom sortable implementation with sort_number detection

## How It Works

### Example Scenario
Database has items with sort_numbers: 5, 6, 7, 10, 15

When filtered, only items 6, 10, 15 are shown.

**Before Fix:**
- Dragging item 15 to position 2 would try to swap with sort_number 2 (doesn't exist in filter)

**After Fix:**
- Dragging item 15 to position 2 reads the next row's `data-sort-number` (10)
- Swaps 15 ↔ 10 correctly
- Result: 6, 15, 10 (with actual sort_numbers preserved)

## Key Code Pattern

```javascript
update: function(event, ui) {
    const draggedRow = ui.item;
    const $dragHandle = draggedRow.find('.drag-handle');
    const draggedId = $dragHandle.data('id');
    const draggedOldSortNumber = $dragHandle.data('sort-number');
    
    let targetSortNumber = null;
    
    // Get next row's sort_number
    const $nextRow = draggedRow.next('tr');
    if ($nextRow.length > 0) {
        targetSortNumber = $nextRow.find('.drag-handle').data('sort-number');
    }
    
    // Fallback to previous row
    if (targetSortNumber === null) {
        const $prevRow = draggedRow.prev('tr');
        if ($prevRow.length > 0) {
            targetSortNumber = $prevRow.find('.drag-handle').data('sort-number');
        }
    }
    
    // Send to backend for swap
    $.ajax({
        url: reorderUrl,
        data: {
            items: [{ id: draggedId, sort_number: targetSortNumber }]
        }
    });
}
```

## Testing Scenarios

### ✅ Scenario 1: No Filters
- All items visible
- Drag & drop works with actual sort_numbers

### ✅ Scenario 2: Search Active
- Subset of items visible
- Drag & drop uses visible items' actual sort_numbers
- No conflict with hidden items

### ✅ Scenario 3: Multiple Filters
- Department + Status filters active
- Only matching items shown
- Drag & drop correctly swaps within filtered set

### ✅ Scenario 4: Sort by Sort Number (Ascending)
- Drag & drop enabled
- Uses actual sort_numbers from data attributes

### ✅ Scenario 5: Sort by Other Column
- Drag & drop disabled
- Warning message shown

## Files Modified

1. `resources/views/components/datatable-wrapper.blade.php`
   - Updated sortable logic to read `data-sort-number` from adjacent rows

2. `Modules/CatalogManagement/resources/views/product/product_configurations_table/_custom-handlers.blade.php`
   - Added `data-sort-number` to drag handle render

3. `Modules/CategoryManagment/resources/views/department/index.blade.php`
   - Added `data-sort-number` to drag handle
   - Updated sortable logic

4. `Modules/CategoryManagment/resources/views/category/index.blade.php`
   - Added `data-sort-number` to drag handle
   - Updated sortable logic

5. `Modules/CategoryManagment/resources/views/subcategory/index.blade.php`
   - Added `data-sort-number` to drag handle
   - Updated sortable logic

## Backend Controllers (Already Implemented)

All controllers use swap logic:
- `ProductController::updateSortOrder()`
- `DepartmentController::reorder()`
- `CategoryController::reorder()`
- `SubCategoryController::reorder()`

## Conclusion

The drag & drop sorting now correctly handles filtered and searched results by using actual database sort_numbers instead of visual positions. The implementation is consistent across all entities and works seamlessly with all filter combinations.
