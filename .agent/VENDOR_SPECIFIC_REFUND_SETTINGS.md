# Vendor-Specific Refund Settings Implementation

## Overview
Implemented vendor-specific refund settings, removing the global system settings. Each vendor now has their own refund configuration including enable/disable toggle, refund days, and customer return shipping payment settings.

## Changes Made

### 1. Database Migration

#### Vendor Refund Settings Table
- **File**: `Modules/Refund/database/migrations/2026_01_19_143401_create_vendor_refund_settings_table.php`
- **Table**: `vendor_refund_settings`
- **Fields**:
  - `vendor_id`: Foreign key to vendors table (unique)
  - `refund_enabled`: Boolean to enable/disable refunds for vendor (default: true)
  - `refund_processing_days`: Integer for refund days (default: 7)
  - `customer_pays_return_shipping`: Boolean for return shipping payment (default: false)
  - `timestamps`

### 2. Models

#### VendorRefundSetting Model
- **File**: `Modules/Refund/app/Models/VendorRefundSetting.php`
- **Features**:
  - Fillable fields: `vendor_id`, `refund_enabled`, `refund_processing_days`, `customer_pays_return_shipping`
  - Casts for boolean and integer types
  - `getForVendor($vendorId)`: Static method to get or create settings for a vendor
  - Relationship: `vendor()` - belongs to Vendor

#### Vendor Model Update
- **File**: `Modules/Vendor/app/Models/Vendor.php`
- Added `refundSettings()` relationship (hasOne)

### 3. Controller

#### VendorRefundSettingController
- **File**: `Modules/Refund/app/Http/Controllers/VendorRefundSettingController.php`
- **Methods**:
  - `index()`: Display vendor refund settings
  - `update()`: Update vendor refund settings
- **Validation**:
  - `refund_enabled`: required|boolean
  - `refund_processing_days`: required|integer|min:1|max:365
  - `customer_pays_return_shipping`: required|boolean
- **Authorization**: Only authenticated vendor users can access their settings

### 4. Routes

#### Updated Routes
- **File**: `Modules/Refund/routes/web.php`
- Removed global system settings routes
- Added vendor-specific routes:
  - `GET /refunds/settings` → `VendorRefundSettingController@index`
  - `PUT /refunds/settings` → `VendorRefundSettingController@update`

### 5. Views

#### Vendor Settings View
- **File**: `Modules/Refund/resources/views/vendor-settings/index.blade.php`
- **Features**:
  - Enable/Disable Refunds switcher (green)
  - Customer Pays Return Shipping switcher (blue)
  - Refund Days input field (1-365 days)
  - Uses form-card-handler component
  - Uses form-switcher and form-input-field components
  - Clean, modern UI matching the system design

### 6. Helper Updates

#### RefundHelper
- **File**: `Modules/Refund/app/Helpers/RefundHelper.php`
- **Updated `getRefundDays()` method**:
  - Priority 1: Product-specific refund days (if set)
  - Priority 2: Vendor-specific refund days (from vendor settings)
  - Priority 3: Default fallback (7 days)
- Removed dependency on global RefundSetting model

### 7. Translation Keys

#### English (`Modules/Refund/lang/en/refund.php`)
```php
'titles' => [
    'vendor_refund_settings' => 'Vendor Refund Settings',
],

'vendor_settings' => [
    'refund_enabled' => 'Enable Refunds',
    'refund_enabled_help' => 'Enable or disable refund requests for your products',
    'vendor_refund_days' => 'Refund Days',
    'vendor_refund_days_help' => 'Number of days after delivery that customers can request refunds for your products',
],
```

#### Arabic (`Modules/Refund/lang/ar/refund.php`)
```php
'titles' => [
    'vendor_refund_settings' => 'إعدادات الاسترجاع للمورد',
],

'vendor_settings' => [
    'refund_enabled' => 'تفعيل الاسترجاع',
    'refund_enabled_help' => 'تفعيل أو تعطيل طلبات الاسترجاع لمنتجاتك',
    'vendor_refund_days' => 'أيام الاسترجاع',
    'vendor_refund_days_help' => 'عدد الأيام بعد التوصيل التي يمكن للعملاء طلب الاسترجاع فيها لمنتجاتك',
],
```

## Settings Hierarchy

The refund days are determined in this order:

1. **Product-Specific**: If a product has `refund_days` set, use it
2. **Vendor-Specific**: If vendor has `refund_processing_days` set, use it
3. **Default**: Fall back to 7 days

## Features

### 1. Enable/Disable Refunds
- Vendors can completely disable refunds for their products
- Toggle switch with green color for easy visibility
- When disabled, customers cannot create refund requests for vendor's products

### 2. Custom Refund Days
- Each vendor sets their own refund period (1-365 days)
- Overrides system default
- Can be further overridden at product level

### 3. Return Shipping Payment
- Vendors decide if customers pay for return shipping
- If enabled, return shipping cost is deducted from refund amount
- Toggle switch with blue color

### 4. Automatic Settings Creation
- Settings are automatically created when vendor first accesses the page
- Default values:
  - `refund_enabled`: true
  - `refund_processing_days`: 7
  - `customer_pays_return_shipping`: false

## Usage Example

### Accessing Vendor Settings
```php
$vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
$settings = VendorRefundSetting::getForVendor($vendor->id);

echo $settings->refund_enabled; // true/false
echo $settings->refund_processing_days; // 7
echo $settings->customer_pays_return_shipping; // true/false
```

### Getting Refund Days for Product
```php
use Modules\Refund\app\Helpers\RefundHelper;

$vendorProduct = VendorProduct::find($id);
$refundDays = RefundHelper::getRefundDays($vendorProduct);
// Returns: product days > vendor days > 7 (default)
```

### Checking if Refunds Enabled
```php
$vendorSettings = VendorRefundSetting::where('vendor_id', $vendorId)->first();
if ($vendorSettings && !$vendorSettings->refund_enabled) {
    // Refunds are disabled for this vendor
}
```

## Benefits

1. **Vendor Autonomy**: Each vendor controls their own refund policy
2. **Flexibility**: Different vendors can have different refund periods
3. **No Global Settings**: Removed dependency on system-wide settings
4. **Easy Management**: Simple UI with toggle switches
5. **Granular Control**: Three levels of control (product > vendor > default)
6. **Return Shipping**: Vendors decide who pays for return shipping

## Files Modified/Created

### Created
1. `Modules/Refund/database/migrations/2026_01_19_143401_create_vendor_refund_settings_table.php`
2. `Modules/Refund/app/Models/VendorRefundSetting.php`
3. `Modules/Refund/app/Http/Controllers/VendorRefundSettingController.php`
4. `Modules/Refund/resources/views/vendor-settings/index.blade.php`

### Modified
1. `Modules/Refund/routes/web.php`
2. `Modules/Refund/app/Helpers/RefundHelper.php`
3. `Modules/Vendor/app/Models/Vendor.php`
4. `Modules/Refund/lang/en/refund.php`
5. `Modules/Refund/lang/ar/refund.php`

## Migration Note

To apply the vendor refund settings table, run:
```bash
php artisan migrate
```

The migration will create the `vendor_refund_settings` table with a unique constraint on `vendor_id` to ensure one setting per vendor.

## UI Components Used

1. **x-breadcrumb**: Navigation breadcrumb
2. **x-form-card-handler**: Form wrapper with AJAX submission
3. **x-form-switcher**: Toggle switches for boolean fields
4. **x-form-input-field**: Number input for refund days

## Route Access

- **URL**: `/admin/refunds/settings`
- **Methods**: GET (view), PUT (update)
- **Authorization**: Vendor users only
- **Middleware**: web, auth, setLanguageCountry, setAdminRouteDefaults, localizationRedirect, localeViewPath
