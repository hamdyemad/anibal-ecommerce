# Batch Progress Inline Component

A reusable component for tracking Laravel batch job progress inline on the page (not in a modal). Progress persists across page navigation using localStorage, allowing users to navigate away and return to check progress.

## Features

- ✅ **Inline Display** - Shows progress directly on the page, not in a modal
- ✅ **Persistent Tracking** - Uses localStorage to persist progress across page navigation
- ✅ **Auto-Resume** - Automatically resumes tracking when returning to the page
- ✅ **Background Processing** - Users can navigate away while import continues
- ✅ **Detailed Progress** - Shows percentage, jobs remaining, and elapsed time
- ✅ **Dismissible** - Users can dismiss completed/failed imports
- ✅ **Customizable** - All texts, intervals, and callbacks are configurable

## Basic Usage

### 1. Include the Component in Your Blade View

```blade
<x-batch-progress-inline 
    containerId="myProgressContainer"
    progressCheckUrl="{{ route('my.batch.progress', ':batchId') }}"
    storageKey="my_import_progress"
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
            // Start inline progress tracking
            BatchProgressInline.start(
                'myProgressContainer',
                response.batch_id,
                '{{ route("my.batch.progress", ":batchId") }}',
                {
                    storageKey: 'my_import_progress',
                    onComplete: function(response) {
                        if (!response.failed) {
                            toastr.success('Import completed!');
                            window.location.reload();
                        }
                    }
                }
            );
        }
    }
});
```

### 3. Resume Progress on Page Load

```javascript
$(document).ready(function() {
    // Resume tracking if there's an ongoing import
    BatchProgressInline.resume(
        'myProgressContainer',
        '{{ route("my.batch.progress", ":batchId") }}',
        {
            storageKey: 'my_import_progress'
        }
    );
});
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `containerId` | string | `'batchProgressContainer'` | Unique container ID |
| `progressCheckUrl` | string | `null` | URL template with `:batchId` placeholder |
| `checkInterval` | int | `2000` | Milliseconds between progress checks |
| `storageKey` | string | `'batch_progress'` | localStorage key for persistence |
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
    'processing' => 'Processing',
    'jobsRemaining' => 'jobs remaining',
]
```

## JavaScript API

### `BatchProgressInline.start(containerId, batchId, progressCheckUrl, options)`

Starts tracking batch progress inline.

**Parameters:**
- `containerId` (string): Container element ID
- `batchId` (string): Batch ID to track
- `progressCheckUrl` (string): URL with `:batchId` placeholder
- `options` (object, optional): Configuration options

**Options:**
```javascript
{
    checkInterval: 2000,
    storageKey: 'batch_progress',
    texts: { ... },
    onComplete: function(response) {},
    onUpdate: function(response) {}
}
```

### `BatchProgressInline.resume(containerId, progressCheckUrl, options)`

Resumes tracking from localStorage (call on page load).

**Parameters:**
- `containerId` (string): Container element ID
- `progressCheckUrl` (string): URL with `:batchId` placeholder
- `options` (object, optional): Configuration options

**Returns:** `boolean` - `true` if resumed, `false` if no progress found

### `BatchProgressInline.stop(containerId, storageKey)`

Stops tracking and clears localStorage.

**Parameters:**
- `containerId` (string): Container element ID
- `storageKey` (string, optional): localStorage key

## Backend Requirements

Progress endpoint should return:

```php
return response()->json([
    'success' => true,
    'progress' => 75,           // 0-100
    'finished' => false,
    'failed' => false,
    'total_jobs' => 100,
    'pending_jobs' => 25,
    'processed_jobs' => 75,
]);
```

## Complete Example: Vendor Bank Products Import

```blade
{{-- In your view --}}
<x-batch-progress-inline 
    containerId="vendorBankProgress"
    progressCheckUrl="{{ route('admin.products.vendor-bank.bulk-upload.progress', ':batchId') }}"
    storageKey="vendor_bank_import_progress"
    :texts="[
        'inProgress' => __('catalogmanagement::product.import_in_progress'),
        'completed' => __('catalogmanagement::product.import_completed'),
        'completedMessage' => __('catalogmanagement::product.import_completed_message'),
        'failed' => __('catalogmanagement::product.import_failed'),
        'failedMessage' => __('catalogmanagement::product.import_failed_message'),
        'processing' => __('common.processing'),
        'jobsRemaining' => __('common.jobs_remaining'),
    ]"
/>

{{-- Upload Form --}}
<form id="uploadForm">
    <input type="file" name="file" id="file">
    <button type="submit">Upload</button>
</form>

@push('scripts')
<script>
$(document).ready(function() {
    // Resume progress on page load
    BatchProgressInline.resume(
        'vendorBankProgress',
        '{{ route("admin.products.vendor-bank.bulk-upload.progress", ":batchId") }}',
        {
            storageKey: 'vendor_bank_import_progress',
            onComplete: function(response) {
                if (!response.failed) {
                    toastr.success('Import completed!');
                    setTimeout(() => window.location.reload(), 2000);
                }
            }
        }
    );
    
    // Handle form submission
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/upload',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success && response.batch_id) {
                    BatchProgressInline.start(
                        'vendorBankProgress',
                        response.batch_id,
                        '{{ route("admin.products.vendor-bank.bulk-upload.progress", ":batchId") }}',
                        {
                            storageKey: 'vendor_bank_import_progress',
                            onComplete: function(response) {
                                if (!response.failed) {
                                    toastr.success('Import completed!');
                                    setTimeout(() => window.location.reload(), 2000);
                                }
                            }
                        }
                    );
                }
            }
        });
    });
});
</script>
@endpush
```

## User Experience Flow

1. **User uploads file** → Progress bar appears inline
2. **User navigates away** → Progress continues in background (stored in localStorage)
3. **User returns to page** → Progress automatically resumes from where it left off
4. **Import completes** → Success message shown, dismiss button appears
5. **User dismisses** → Progress bar slides up and localStorage is cleared

## Persistence Details

- Progress is stored in `localStorage` with the specified `storageKey`
- Stored data includes: `containerId`, `batchId`, `progressCheckUrl`, `startTime`, `config`
- Progress older than 24 hours is automatically discarded
- Clearing localStorage or dismissing removes the progress

## Styling

The component uses Bootstrap 5 classes. Customize with CSS:

```css
#myProgressContainer .card {
    border-left: 4px solid #007bff;
}

#myProgressContainer .progress {
    height: 30px;
}

#myProgressContainer .progress-bar {
    font-size: 16px;
}
```

## Comparison: Modal vs Inline

| Feature | Modal | Inline |
|---------|-------|--------|
| Display | Blocks page | Shows on page |
| Navigation | Blocks navigation | Allows navigation |
| Persistence | No | Yes (localStorage) |
| Resume | No | Yes |
| Dismissible | No | Yes |
| Use Case | Quick imports | Long-running imports |

## Best Practices

1. **Use inline for long-running imports** (>30 seconds)
2. **Use modal for quick imports** (<30 seconds)
3. **Always call `resume()` on page load** to restore progress
4. **Use unique `storageKey`** for different import types
5. **Provide clear completion callbacks** for user feedback
6. **Test navigation** to ensure progress persists correctly

## Troubleshooting

**Progress doesn't resume:**
- Check if `storageKey` matches between start and resume
- Verify localStorage is enabled in browser
- Check if progress is older than 24 hours (auto-cleared)

**Progress bar doesn't update:**
- Verify backend returns correct JSON format
- Check `progressCheckUrl` has `:batchId` placeholder
- Ensure `checkInterval` is reasonable (2000ms recommended)

**Multiple progress bars:**
- Use unique `containerId` for each
- Use unique `storageKey` for each
- Each operates independently
