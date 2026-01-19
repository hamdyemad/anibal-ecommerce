# Notification System - Centralized Architecture

## Overview
All notifications are now centralized in the `admin_notifications` table. Each model has its own observer that creates notifications automatically.

## Architecture

### Database
- **Table**: `admin_notifications`
- **Fields**: type, icon, color, title, description, url, notifiable_type, notifiable_id, user_id, vendor_id, data, is_read, read_at

### Observers Created

1. **OrderObserver** (`Modules/Order/app/Observers/OrderObserver.php`)
   - Creates notification when new order is created
   - Sends to all vendors involved in the order
   - Sends to admin
   - Icon: `uil-shopping-bag`, Color: `primary`

2. **VendorObserver** (`Modules/Vendor/app/Observers/VendorObserver.php`)
   - Creates notification when vendor requests to join (active = 0)
   - Marks notification as read when vendor is activated
   - Icon: `uil-user-plus`, Color: `warning`

3. **MessageObserver** (`Modules/SystemSetting/app/Observers/MessageObserver.php`)
   - Creates notification for new pending messages
   - Marks notification as read when message status changes
   - Icon: `uil-envelope`, Color: `success`

4. **RequestQuotationObserver** (`Modules/Order/app/Observers/RequestQuotationObserver.php`)
   - Creates notification for new quotation requests
   - Creates notification when offer is accepted (icon: `uil-check-circle`, color: `success`)
   - Creates notification when offer is rejected (icon: `uil-times-circle`, color: `danger`)
   - Default icon: `uil-file-question-alt`, Color: `warning`

5. **RefundRequestObserver** (`Modules/Refund/app/Observers/RefundRequestObserver.php`)
   - Creates notification when refund is created
   - Creates notification when refund status changes
   - Uses RefundNotificationService for icon/color based on status

### Notification View
- **File**: `resources/views/partials/top_nav/_notifications.blade.php`
- **Query**: Fetches from `admin_notifications` table only
- **Filtering**:
  - Vendors: Shows notifications where `vendor_id` matches or is null
  - Admins: Shows notifications where `vendor_id` is null
- **Display**: Shows icon, color, title, description from database

## Benefits

1. **Centralized**: All notifications in one table
2. **Consistent**: Same structure for all notification types
3. **Maintainable**: Each model manages its own notifications via observers
4. **Flexible**: Easy to add new notification types
5. **Scalable**: Can add read/unread tracking, filtering, etc.

## How to Add New Notification Type

1. Create observer for the model
2. In observer's `created()` or `updated()` method, call:
   ```php
   AdminNotification::notify(
       type: 'notification_type',
       title: 'Title',
       description: 'Description',
       url: route('route.name', $id),
       icon: 'uil-icon-name',
       color: 'primary|success|warning|danger|info',
       notifiable: $model,
       data: ['key' => 'value'],
       vendorId: $vendorId // or null for admin
   );
   ```
3. Register observer in service provider's `boot()` method
4. Done! Notification will automatically appear in the bell dropdown

## Files Modified

- `resources/views/partials/top_nav/_notifications.blade.php` - Simplified to only query admin_notifications
- `Modules/Order/app/Observers/OrderObserver.php` - Added notification creation
- `Modules/Vendor/app/Observers/VendorObserver.php` - Created new
- `Modules/SystemSetting/app/Observers/MessageObserver.php` - Created new
- `Modules/Order/app/Observers/RequestQuotationObserver.php` - Created new
- `Modules/Refund/app/Observers/RefundRequestObserver.php` - Already had notification creation
- Service providers - Registered new observers

## Status
✅ Complete - All notifications now centralized in admin_notifications table
