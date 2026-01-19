# Refund Status Steps and History Implementation

## Overview
Implemented status workflow steps, history tracking, and country support for the refund system.

## Changes Made

### 1. Database Migrations

#### Country Support
- **File**: `Modules/Refund/database/migrations/2026_01_19_143001_add_country_id_to_refund_requests_table.php`
- Added `country_id` foreign key to `refund_requests` table
- Links refunds to countries for multi-country support

#### History Tracking
- **File**: `Modules/Refund/database/migrations/2026_01_19_143012_create_refund_request_histories_table.php`
- Created `refund_request_histories` table with fields:
  - `refund_request_id`: Links to refund request
  - `old_status`: Previous status (nullable for initial creation)
  - `new_status`: New status
  - `user_id`: User who made the change (nullable for system changes)
  - `notes`: Optional notes about the change
  - `timestamps`: When the change occurred

### 2. Models

#### RefundRequestHistory Model
- **File**: `Modules/Refund/app/Models/RefundRequestHistory.php`
- Tracks all status changes for refund requests
- Relationships:
  - `refundRequest()`: Belongs to RefundRequest
  - `user()`: Belongs to User who made the change

#### RefundRequest Model Updates
- **File**: `Modules/Refund/app/Models/RefundRequest.php`
- Added `HasCountries` trait for country filtering
- Added `country_id` to fillable fields
- Added relationships:
  - `country()`: Belongs to Country
  - `history()`: Has many RefundRequestHistory records (ordered by created_at desc)

### 3. Repository Updates

#### RefundRequestRepository
- **File**: `Modules/Refund/app/Repositories/RefundRequestRepository.php`
- Added `createHistoryRecord()` method to track status changes
- Updated methods to create history records:
  - `updateRefundStatus()`: Creates history when status changes
  - `approveRefund()`: Creates history for approval
  - `rejectRefund()`: Creates history for rejection with notes
  - `cancelRefund()`: Creates history for cancellation
- Updated `createRefundWithVendorSplit()` to include `country_id` from order

### 4. View Updates

#### Refund Show View
- **File**: `Modules/Refund/resources/views/refund-requests/show.blade.php`
- **Status Steps Workflow**:
  - `pending` → Can go to: `approved`, `rejected`, `cancelled`
  - `approved` → Can go to: `in_progress`
  - `in_progress` → Can go to: `picked_up`
  - `picked_up` → Can go to: `refunded`
  - `rejected`, `cancelled`, `refunded` → Final states (no further actions)

- **Action Buttons**:
  - Color-coded buttons for each status transition
  - Approve: Green button
  - Reject: Red button (opens modal for reason)
  - Cancel: Gray button
  - In Progress: Blue button
  - Picked Up: Info button
  - Refunded: Success button

- **History Section**:
  - Timeline-style display of all status changes
  - Shows old status → new status transition
  - Displays user who made the change
  - Shows timestamp of change
  - Includes notes if provided
  - Styled with vertical timeline connector

- **CSS Styling**:
  - Added timeline styles with vertical line connector
  - Marker dots for each history entry
  - Color-coded status badges
  - Responsive design

### 5. Translation Keys

#### English (`Modules/Refund/lang/en/refund.php`)
- Added `status_history` to titles

#### Arabic (`Modules/Refund/lang/ar/refund.php`)
- Added `سجل الحالات` (status_history) to titles

### 6. Controller Updates

#### RefundRequestController
- **File**: `Modules/Refund/app/Http/Controllers/RefundRequestController.php`
- Updated `show()` method to eager load `history.user` relationship

## Status Workflow

```
pending
├── approved → in_progress → picked_up → refunded
├── rejected (final)
└── cancelled (final)
```

### Status Descriptions

1. **pending**: Initial state when refund is created
   - Customer can cancel
   - Vendor/Admin can approve or reject

2. **approved**: Refund has been approved
   - Next step: Mark as in_progress

3. **in_progress**: Refund is being processed
   - Next step: Mark as picked_up

4. **picked_up**: Product has been picked up from customer
   - Next step: Mark as refunded

5. **refunded**: Money has been refunded to customer (final state)

6. **rejected**: Refund request was rejected (final state)

7. **cancelled**: Customer cancelled the refund (final state)

## Features

### History Tracking
- Every status change is automatically recorded
- Tracks who made the change (user_id)
- Records old and new status
- Stores optional notes (especially for rejections)
- Timestamps for audit trail

### Country Support
- Refunds are linked to countries via `country_id`
- Inherited from the order's country
- Enables country-specific filtering using `HasCountries` trait
- Supports multi-country operations

### Status Steps UI
- Only shows valid next steps based on current status
- No actions shown for final states (rejected, cancelled, refunded)
- Color-coded buttons for easy identification
- Modal for rejection to capture reason

### Timeline Display
- Visual timeline showing status progression
- Old status → New status transitions
- User attribution for each change
- Timestamp for each change
- Notes display for additional context

## Usage Example

### Viewing Refund with History
```php
$refund = RefundRequest::with(['history.user'])->find($id);

foreach ($refund->history as $history) {
    echo "{$history->old_status} → {$history->new_status}";
    echo " by {$history->user->name}";
    echo " at {$history->created_at}";
}
```

### Changing Status (Automatically Creates History)
```php
$refundService->updateRefundStatus($refundId, [
    'status' => 'in_progress',
    'notes' => 'Processing refund'
], $user);
```

## Benefits

1. **Audit Trail**: Complete history of all status changes
2. **Accountability**: Know who made each change
3. **Transparency**: Customers and vendors can see status progression
4. **Workflow Control**: Only valid transitions are allowed
5. **Country Support**: Multi-country refund management
6. **User-Friendly**: Clear visual representation of status flow

## Files Modified

1. `Modules/Refund/database/migrations/2026_01_19_143001_add_country_id_to_refund_requests_table.php` (new)
2. `Modules/Refund/database/migrations/2026_01_19_143012_create_refund_request_histories_table.php` (new)
3. `Modules/Refund/app/Models/RefundRequestHistory.php` (new)
4. `Modules/Refund/app/Models/RefundRequest.php` (updated)
5. `Modules/Refund/app/Repositories/RefundRequestRepository.php` (updated)
6. `Modules/Refund/app/Http/Controllers/RefundRequestController.php` (updated)
7. `Modules/Refund/resources/views/refund-requests/show.blade.php` (updated)
8. `Modules/Refund/lang/en/refund.php` (updated)
9. `Modules/Refund/lang/ar/refund.php` (updated)
