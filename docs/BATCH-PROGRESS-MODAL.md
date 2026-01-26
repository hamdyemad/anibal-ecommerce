# Batch Progress Modal Component

A reusable component for tracking Laravel batch job progress with a modal UI.

## Features

- ✅ Reusable across any page with batch jobs
- ✅ Customizable texts and styling
- ✅ Automatic progress tracking
- ✅ Success/failure states
- ✅ Configurable check intervals
- ✅ Custom completion callbacks

## Basic Usage

### 1. Include the Component in Your Blade View

```blade
<x-batch-progress-modal 
    modalId="myProgressModal"
    progressCheckUrl="{{ route('my.batch.progress', ':batchId') }}"
/>
```

### 2. Start Progress Tracking from JavaScript

```javascript
// After your AJAX request returns a batch_id
$.ajax({
    url: '/my-batch-endpoint',
    type: 'POST',
    data: formData,
    success: function(response) {
        if (response.success && response.batch_id) {
            // Start tracking progress
            BatchProgressModal.start(
                'myProgressModal',
                response.batch_id,
                '{{ route("my.batch.progress", ":batchId") }}'
            );
        }
    }
});
```

## Advanced Usage

### Custom Configuration

```blade
<x-batch-progress-modal 
    modalId="importProgressModal"
    progressCheckUrl="{{ route('admin.products.bulk-upload.progress', ':batchId') }}"
    checkInterval="1000"
    onComplete="handleImportComplete"
    :texts="[
        'inProgress' => 'Importing Products...',
        'checking' => 'Processing your file...',
        'completed' => 'Import Successful!',
        'completedMessage' => 'All products have been imported',
        'failed' => 'Import Failed',
        'failedMessage' => 'Please check the errors below',
        'error' => 'Connection error occurred',
    ]"
/>
```

### Custom Completion Handler

```javascript
function handleImportComplete(response) {
    if (response.failed) {
        // Handle failure
        window.location.href = '/import-errors';
    } else {
        // Handle success
        toastr.success('Import completed successfully!');
        window.location.reload();
    }
}

// Start with custom options
BatchProgressModal.start(
    'importProgressModal',
    batchId,
    progressUrl,
    {
        checkInterval: 1000, // Check every second
        onComplete: handleImportComplete,
        texts: {
            inProgress: 'Custom progress text...',
            // ... other custom texts
        }
    }
);
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `modalId` | string | `'batchProgressModal'` | Unique ID for the modal |
| `progressCheckUrl` | string | `null` | URL template with `:batchId` placeholder |
| `onComplete` | string | `'window.location.reload()'` | JavaScript code to execute on completion |
| `checkInterval` | int | `2000` | Milliseconds between progress checks |
| `texts` | array | See below | Customizable text labels |

### Default Texts

```php
[
    'inProgress' => 'Import in Progress',
    'checking' => 'Checking progress...',
    'completed' => 'Import Completed',
    'completedMessage' => 'Your import has been completed successfully',
    'failed' => 'Import Failed',
    'failedMessage' => 'Some errors occurred during import',
    'error' => 'Error checking progress',
]
```

## Backend Requirements

Your progress check endpoint should return JSON in this format:

```php
return response()->json([
    'success' => true,
    'progress' => 75, // 0-100
    'finished' => false,
    'failed' => false,
    'total_jobs' => 100,
    'pending_jobs' => 25,
    'processed_jobs' => 75,
]);
```

When finished:

```php
return response()->json([
    'success' => true,
    'progress' => 100,
    'finished' => true,
    'failed' => false, // or true if failed
]);
```

## Example: Vendor Bank Products Import

```blade
{{-- In your view --}}
<x-batch-progress-modal 
    modalId="vendorBankProgressModal"
    progressCheckUrl="{{ route('admin.products.vendor-bank.bulk-upload.progress', ':batchId') }}"
    :texts="[
        'inProgress' => __('catalogmanagement::product.import_in_progress'),
        'checking' => __('catalogmanagement::product.checking_progress'),
        'completed' => __('catalogmanagement::product.import_completed'),
        'completedMessage' => __('catalogmanagement::product.import_completed_message'),
        'failed' => __('catalogmanagement::product.import_failed'),
        'failedMessage' => __('catalogmanagement::product.import_failed_message'),
        'error' => __('catalogmanagement::product.error_checking_progress'),
    ]"
/>

{{-- In your JavaScript --}}
<script>
$('#uploadForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success && response.batch_id) {
                BatchProgressModal.start(
                    'vendorBankProgressModal',
                    response.batch_id,
                    '{{ route("admin.products.vendor-bank.bulk-upload.progress", ":batchId") }}'
                );
            }
        }
    });
});
</script>
```

## API Methods

### `BatchProgressModal.start(modalId, batchId, progressCheckUrl, options)`

Starts tracking batch progress.

**Parameters:**
- `modalId` (string): The modal element ID
- `batchId` (string): The batch ID to track
- `progressCheckUrl` (string): URL with `:batchId` placeholder
- `options` (object, optional): Configuration options

**Options:**
```javascript
{
    checkInterval: 2000,        // Check interval in ms
    onComplete: function(response) {}, // Completion callback
    texts: { ... }              // Custom text labels
}
```

### `BatchProgressModal.stop(modalId)`

Stops tracking and hides the modal.

**Parameters:**
- `modalId` (string): The modal element ID

## Styling

The component uses Bootstrap 5 modal and progress bar classes. You can customize the appearance by overriding these classes in your CSS:

```css
#myProgressModal .modal-content {
    border-radius: 15px;
}

#myProgressModal .progress {
    height: 40px;
    border-radius: 20px;
}

#myProgressModal .progress-bar {
    font-size: 18px;
}
```

## Multiple Modals

You can use multiple progress modals on the same page:

```blade
<x-batch-progress-modal modalId="importModal" />
<x-batch-progress-modal modalId="exportModal" />
<x-batch-progress-modal modalId="syncModal" />
```

Each modal operates independently with its own progress tracking.
