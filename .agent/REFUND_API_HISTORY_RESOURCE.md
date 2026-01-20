# Refund API History Resource - Implementation

## Overview
Added history relationship to the RefundRequestResource API to include status change history with each refund request.

## Changes Made

### 1. Created RefundRequestHistoryResource
**File**: `Modules/Refund/app/Http/Resources/RefundRequestHistoryResource.php`

New resource to transform refund history records for API responses:

**Fields Included**:
- `id`: History record ID
- `refund_request_id`: Parent refund request ID
- `old_status`: Previous status code
- `old_status_label`: Translated previous status label
- `new_status`: New status code
- `new_status_label`: Translated new status label
- `user_id`: ID of user who made the change
- `user_name`: Name of user who made the change
- `notes`: Additional notes about the status change
- `created_at`: ISO 8601 timestamp
- `created_at_human`: Human-readable time difference (e.g., "2 hours ago")

### 2. Updated RefundRequestResource
**File**: `Modules/Refund/app/Http/Resources/RefundRequestResource.php`

**Added**:
- `history` relationship using `RefundRequestHistoryResource::collection()`
- `status_icon`: Icon class for the current status
- `status_color`: Color class for the current status
- `created_at_human`: Human-readable creation time
- All timestamp fields now use `toISOString()` for consistent API format

**Fixed**:
- Changed `$this->getStatusIcon()` to `$this->resource->getStatusIcon()` (instance method)
- Changed `$this->getStatusConfig()` to static call `\Modules\Refund\app\Models\RefundRequest::getStatusConfig()`
- Restored missing timestamp fields (updated_at, approved_at, refunded_at)

### 3. Updated API Controller
**File**: `Modules/Refund/app/Http/Controllers/Api/RefundRequestApiController.php`

Updated `show()` method to eager load relationships:
```php
$refund = $this->refundService->getRefundWithRelations($id, [
    'items',
    'history.user',  // Load history with user who made changes
    'order',
    'customer',
    'vendor'
]);
```

## API Response Structure

### Single Refund Request (GET /api/refunds/{id})
```json
{
    "status": true,
    "message": "Refund request retrieved successfully",
    "data": {
        "id": 1,
        "refund_number": "REF-20260120-0001",
        "status": "approved",
        "status_label": "Approved",
        "status_icon": "uil-check",
        "status_color": "info",
        "total_refund_amount": 150.50,
        "created_at": "2026-01-20T10:30:00.000000Z",
        "created_at_human": "2 hours ago",
        "items": [...],
        "history": [
            {
                "id": 1,
                "old_status": null,
                "old_status_label": null,
                "new_status": "pending",
                "new_status_label": "Pending",
                "user_id": 5,
                "user_name": "John Doe",
                "notes": "Refund request created",
                "created_at": "2026-01-20T10:30:00.000000Z",
                "created_at_human": "2 hours ago"
            },
            {
                "id": 2,
                "old_status": "pending",
                "old_status_label": "Pending",
                "new_status": "approved",
                "new_status_label": "Approved",
                "user_id": 1,
                "user_name": "Admin User",
                "notes": "Approved by admin",
                "created_at": "2026-01-20T11:00:00.000000Z",
                "created_at_human": "1 hour ago"
            }
        ]
    }
}
```

## Benefits

1. **Complete History**: Clients can see full status change timeline
2. **User Tracking**: Know who made each status change
3. **Timestamps**: Both ISO format and human-readable
4. **Translations**: Status labels are translated based on API locale
5. **Lazy Loading**: History only loaded when explicitly requested (using `whenLoaded`)
6. **Consistent Format**: All timestamps use ISO 8601 format

## Usage Examples

### Mobile App - Display Timeline
```javascript
// Show refund status history timeline
refund.history.forEach(historyItem => {
    console.log(`${historyItem.created_at_human}: ${historyItem.new_status_label}`);
    console.log(`Changed by: ${historyItem.user_name}`);
    if (historyItem.notes) {
        console.log(`Notes: ${historyItem.notes}`);
    }
});
```

### Frontend - Status Badge
```javascript
// Use status icon and color from API
<span class="badge bg-${refund.status_color}">
    <i class="${refund.status_icon}"></i>
    ${refund.status_label}
</span>
```

## Related Files

- `Modules/Refund/app/Models/RefundRequest.php` - Model with history relationship
- `Modules/Refund/app/Models/RefundRequestHistory.php` - History model
- `Modules/Refund/app/Observers/RefundRequestObserver.php` - Creates history records on status changes

## Testing

Test the API endpoint:
```bash
GET /api/refunds/1
Authorization: Bearer {token}
```

Expected response includes `history` array with all status changes.
