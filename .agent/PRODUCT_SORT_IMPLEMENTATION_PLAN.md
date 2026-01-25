# Product Sort Number Implementation Plan

Based on the department implementation, here's what needs to be added to products:

## 1. Database Changes
- Add `sort_number` column to `vendor_products` table (integer, default 0)
- Migration needed

## 2. Backend Changes

### Controller
- Add sort parameters to datatable method
- Add update-sort-order endpoint for drag & drop

### Action/Repository
- Handle `sort_column` and `sort_direction` parameters
- Default sort by `sort_number ASC`
- Update sort order when dragging

## 3. Frontend Changes

### Filters (_filters.blade.php)
Add sort filters:
```blade
{{-- Sort By Filter --}}
<div class="col-md-3">
    <div class="form-group">
        <label for="sort_column" class="il-gray fs-14 fw-500 mb-10">
            {{ __('common.sort_by') ?? 'Sort By' }}
        </label>
        <select class="form-control form-select ih-medium ip-gray radius-xs b-light" id="sort_column">
            <option value="sort_number" selected>{{ __('common.sort_number') ?? 'Sort Number' }}</option>
            <option value="created_at">{{ __('common.created_at') ?? 'Created At' }}</option>
        </select>
    </div>
</div>

{{-- Sort Direction Filter --}}
<div class="col-md-3">
    <div class="form-group">
        <label for="sort_direction" class="il-gray fs-14 fw-500 mb-10">
            {{ __('common.sort_direction') ?? 'Sort Direction' }}
        </label>
        <select class="form-control form-select ih-medium ip-gray radius-xs b-light" id="sort_direction">
            <option value="asc" selected>{{ __('common.ascending') ?? 'Ascending' }}</option>
            <option value="desc">{{ __('common.descending') ?? 'Descending' }}</option>
        </select>
    </div>
</div>
```

### Table Headers
Add drag handle column:
```blade
['label' => '<i class="uil uil-sort"></i>', 'class' => 'text-center', 'style' => 'width: 40px;', 'raw' => true],
```

### Render Functions (_datatable-scripts.blade.php)
Add drag handle render function:
```javascript
window.renderDragHandle = function(data, type, row) {
    return `<div class="drag-handle" style="cursor: grab;">
        <i class="uil uil-draggabledots" style="font-size: 20px; color: #666;"></i>
    </div>`;
};
```

Display sort_number in product information

### Custom Handlers (_custom-handlers.blade.php)
- Add drag handle column to columns array
- Initialize jQuery UI sortable
- Handle sort filter changes
- Update drag/drop state based on sort filters
- AJAX call to update sort order

### Styles
Add CSS for drag & drop:
```css
#productsDataTable tbody tr {
    cursor: default;
}
#productsDataTable tbody tr.ui-sortable-helper {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    cursor: grabbing;
}
#productsDataTable tbody tr.ui-sortable-placeholder {
    border: 2px dashed #2196f3 !important;
    visibility: visible !important;
}
```

## 4. Form Changes (create.blade.php / edit.blade.php)
Add sort_number field:
```blade
<div class="col-md-6 mb-25">
    <div class="form-group">
        <label for="sort_number" class="il-gray fs-14 fw-500 mb-10">
            {{ trans('catalogmanagement::product.sort_number') ?? 'Sort Number' }}
        </label>
        <input type="number"
            class="form-control ih-medium ip-gray radius-xs b-light px-15"
            id="sort_number" name="sort_number"
            value="{{ old('sort_number', $product->sort_number ?? 0) }}" min="0">
    </div>
</div>
```

## 5. Key Features
- Drag & drop only enabled when sorting by sort_number (ASC)
- Visual feedback (drag handle, placeholder, helper)
- Auto-save on drop
- Info message when drag/drop disabled
- Works for both admin and vendor

## Implementation Order
1. Create migration for sort_number column
2. Update backend (controller, action, repository)
3. Add sort filters to frontend
4. Add drag handle column
5. Implement jQuery UI sortable
6. Add sort_number to forms
7. Test drag & drop functionality
