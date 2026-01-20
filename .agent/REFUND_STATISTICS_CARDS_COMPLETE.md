# Refund Statistics Cards - Complete Implementation

## Overview
Implemented dynamic statistics cards for the refund index page that display count and amount for each refund status. All status configurations (icons and colors) are centralized in the model for easy maintenance.

## Implementation Details

### 1. Model Layer - Status Configurations
**File**: `Modules/Refund/app/Models/RefundRequest.php`

Added `STATUS_CONFIGS` constant with icon and color for each status:
```php
const STATUS_CONFIGS = [
    'pending' => ['icon' => 'uil-clock', 'color' => 'warning'],
    'approved' => ['icon' => 'uil-check', 'color' => 'info'],
    'in_progress' => ['icon' => 'uil-sync', 'color' => 'primary'],
    'picked_up' => ['icon' => 'uil-package', 'color' => 'secondary'],
    'refunded' => ['icon' => 'uil-check-circle', 'color' => 'success'],
    'cancelled' => ['icon' => 'uil-ban', 'color' => 'danger'],
];
```

Added helper methods:
- `getStatusConfig($status)`: Get icon and color for a specific status
- `getAllStatusConfigs()`: Get all status configurations
- Updated `getStatusIcon()`, `getStatusTextColor()`, `getStatusBackgroundColor()` to use STATUS_CONFIGS

### 2. Service Layer Update
**File**: `Modules/Refund/app/Services/RefundRequestService.php`

Updated `getRefundStatistics()` method to:
- Loop through all statuses from `RefundRequest::STATUSES` constant
- Calculate count and total amount for each status dynamically
- Return structured data with `status_data` array containing:
  - `count`: Number of refunds in this status
  - `amount`: Raw total amount for this status
  - `amount_formatted`: Formatted amount with 2 decimal places

**Return Structure**:
```php
[
    'total_refunds' => 150,
    'total_refunded_amount' => '45,000.00',
    'status_data' => [
        'pending' => [
            'count' => 25,
            'amount' => 7500.50,
            'amount_formatted' => '7,500.50'
        ],
        'approved' => [...],
        'in_progress' => [...],
        'picked_up' => [...],
        'refunded' => [...],
        'cancelled' => [...]
    ]
]
```

### 3. View Layer Update
**File**: `Modules/Refund/resources/views/refund-requests/index.blade.php`

Replaced hardcoded cards with dynamic loop:
- Loop through `$statistics['status_data']` to create cards dynamically
- Get icon and color from model using `RefundRequest::getStatusConfig($status)`
- Each card displays:
  - Status name (translated)
  - Count of refunds
  - Total amount for that status
  - Appropriate icon and color based on status

### 4. Card Layout
- Cards are displayed in a responsive grid (col-xl-4 col-md-6)
- 3 cards per row on large screens, 2 per row on medium screens
- Each card shows:
  - Status label at top
  - Count as main number (with counter animation)
  - Amount below count in smaller text
  - Icon with colored background on the right

## Benefits

1. **Fully Dynamic**: All configurations come from the model
2. **Single Source of Truth**: STATUS_CONFIGS constant in model
3. **Easy Maintenance**: Change icon/color in one place, affects all views
4. **Consistent**: Same icons/colors used throughout the application
5. **Scalable**: Add new status with icon/color in model, automatically appears everywhere
6. **Reusable**: Other views can use `getStatusConfig()` method

## How to Add a New Status

1. Add status to `STATUSES` constant in model
2. Add status configuration to `STATUS_CONFIGS` constant
3. Add translation in language files
4. Done! The status will automatically appear in:
   - Statistics cards
   - Status badges
   - Filters
   - All other views using the model methods

**Example**:
```php
// In RefundRequest model
const STATUSES = [
    // ... existing statuses
    'rejected' => 'refund::refund.statuses.rejected',
];

const STATUS_CONFIGS = [
    // ... existing configs
    'rejected' => [
        'icon' => 'uil-times-circle',
        'color' => 'danger',
    ],
];
```

## Translation Support

All translations already exist in:
- `Modules/Refund/lang/en/refund.php`
- `Modules/Refund/lang/ar/refund.php`

Status translations are under `'statuses'` key and automatically used via:
```php
trans('refund::refund.statuses.' . $status)
```

## Testing Checklist

- [x] Service returns correct structure
- [x] View loops through all statuses
- [x] Cards display count and amount
- [x] Icons and colors come from model
- [x] Translations work for both EN and AR
- [x] Responsive layout works on different screen sizes
- [x] No syntax errors in PHP or Blade
- [x] Existing methods use STATUS_CONFIGS

## Files Modified

1. `Modules/Refund/app/Models/RefundRequest.php` - Added STATUS_CONFIGS constant and helper methods
2. `Modules/Refund/app/Services/RefundRequestService.php` - Dynamic statistics calculation
3. `Modules/Refund/resources/views/refund-requests/index.blade.php` - Dynamic card rendering

## Related Documentation

- `.agent/REFUND_COMPLETION_AND_WITHDRAW_INTEGRATION.md` - Refund financial integration
- `.agent/REFUND_STATUS_STEPS_AND_HISTORY.md` - Status workflow
