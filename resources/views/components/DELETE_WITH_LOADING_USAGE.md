# Delete with Loading Component Usage Guide

## Overview
The `delete-with-loading` component combines a delete confirmation modal with an animated loading overlay, providing a smooth user experience when deleting records from DataTables.

## Features
- ✅ Delete confirmation modal
- ✅ Animated loading overlay with progress bar
- ✅ Success animation with checkmark
- ✅ Automatic DataTable reload
- ✅ Fully customizable text and classes
- ✅ Reusable across multiple tables

## Basic Usage

### 1. Include the Component in Your View

```blade
<x-delete-with-loading
    modalId="modal-delete-activity"
    tableId="activitiesDataTable"
    deleteButtonClass="delete-activity"
    :title="__('activity.confirm_delete')"
    :message="__('activity.delete_confirmation')"
    itemNameId="delete-activity-name"
    confirmBtnId="confirmDeleteBtn"
    :cancelText="__('activity.cancel')"
    :deleteText="__('activity.delete_activity')"
    :loadingDeleting="trans('loading.deleting') ?? 'Deleting...'"
    :loadingPleaseWait="trans('loading.please_wait') ?? 'Please wait...'"
    :loadingDeletedSuccessfully="trans('loading.deleted_successfully') ?? 'Deleted Successfully!'"
    :loadingRefreshing="trans('loading.refreshing') ?? 'Refreshing...'"
    :errorDeleting="__('activity.error_deleting_activity')"
/>
```

### 2. Include Loading Overlay Component

```blade
@push('after-body')
    <x-loading-overlay />
@endpush
```

### 3. Add Delete Button in Your DataTable Controller

Your delete button must have these data attributes:
- `data-id`: The item ID
- `data-name`: The item name (displayed in confirmation)
- `data-url`: The delete endpoint URL

Example in Controller:

```php
$actions = '
    <button type="button" 
            class="btn btn-sm btn-icon btn-outline-danger delete-activity" 
            data-id="' . $item->id . '" 
            data-name="' . e($item->name) . '"
            data-url="' . route('admin.items.destroy', $item->id) . '"
            title="Delete">
        <i class="uil uil-trash-alt m-0"></i>
    </button>
';
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `modalId` | string | `'deleteModal'` | Unique ID for the modal |
| `tableId` | string | `'dataTable'` | ID of the DataTable to reload |
| `deleteButtonClass` | string | `'delete-btn'` | CSS class for delete buttons |
| `title` | string | `'Confirm Delete'` | Modal title |
| `message` | string | `'Are you sure...'` | Confirmation message |
| `itemNameId` | string | `'delete-item-name'` | ID for item name display |
| `confirmBtnId` | string | `'confirmDeleteBtn'` | ID for confirm button |
| `cancelText` | string | `'Cancel'` | Cancel button text |
| `deleteText` | string | `'Delete'` | Delete button text |
| `loadingDeleting` | string | `'Deleting...'` | Loading text |
| `loadingPleaseWait` | string | `'Please wait...'` | Loading subtext |
| `loadingDeletedSuccessfully` | string | `'Deleted Successfully!'` | Success message |
| `loadingRefreshing` | string | `'Refreshing...'` | Refresh message |
| `errorDeleting` | string | `'Error deleting item'` | Error message |

## Example: Using in Categories Table

```blade
{{-- In your view --}}
<x-delete-with-loading
    modalId="modal-delete-category"
    tableId="categoriesDataTable"
    deleteButtonClass="delete-category"
    :title="__('category.confirm_delete')"
    :message="__('category.delete_confirmation')"
    itemNameId="delete-category-name"
    confirmBtnId="confirmDeleteCategoryBtn"
    :cancelText="__('common.cancel')"
    :deleteText="__('category.delete')"
    :loadingDeleting="trans('loading.deleting')"
    :loadingPleaseWait="trans('loading.please_wait')"
    :loadingDeletedSuccessfully="trans('loading.deleted_successfully')"
    :loadingRefreshing="trans('loading.refreshing')"
    :errorDeleting="__('category.error_deleting')"
/>
```

```php
// In your CategoryController datatable method
$actions = '
    <button type="button" 
            class="btn btn-sm btn-outline-danger delete-category" 
            data-id="' . $category->id . '" 
            data-name="' . e($category->name) . '"
            data-url="' . route('admin.categories.destroy', $category->id) . '"
            title="Delete">
        <i class="uil uil-trash-alt"></i>
    </button>
';
```

## Custom Event Handling

The component triggers a custom event when an item is deleted:

```javascript
$(document).on('itemDeleted', function(event, response) {
    console.log('Item deleted:', response);
    // Add your custom logic here
});
```

## Notes

- Ensure your delete endpoint returns a JSON response with `success: true`
- The component requires jQuery and Bootstrap 5 modal
- The loading overlay uses CSS animations defined in the loading-overlay component
- Each table on a page should have unique IDs for the modal and buttons

## Translation Keys

Add these to your language files:

```php
// loading.php
'deleting' => 'Deleting...',
'deleted_successfully' => 'Deleted Successfully!',
'refreshing' => 'Refreshing...',
'please_wait' => 'Please wait...',
```

## Troubleshooting

### Modal not showing
- Check that modalId is unique on the page
- Verify Bootstrap JS is loaded

### Loading overlay not appearing
- Ensure `<x-loading-overlay />` is included
- Check browser console for JavaScript errors

### DataTable not reloading
- Verify tableId matches your DataTable ID
- Ensure DataTable is initialized before delete action
