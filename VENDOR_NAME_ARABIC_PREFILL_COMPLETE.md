# Vendor Name Arabic Pre-fill - Complete

## Issue
When creating a vendor from a vendor request, only the English name field was being pre-filled with the `company_name` from the vendor request. The Arabic name field remained empty, requiring manual entry.

## Root Cause
The form blade file had a condition that only pre-filled the name when `$language->code == 'en'`:

```blade
value="{{ ... ?? ($language->code == 'en' && $vendorRequest ? $vendorRequest->company_name : '') }}"
```

This meant:
- English field: Pre-filled with `$vendorRequest->company_name` ✓
- Arabic field: Empty (condition failed) ✗

## Solution
Removed the language code check so both English and Arabic fields are pre-filled with the same `company_name` value:

```blade
value="{{ ... ?? ($vendorRequest ? $vendorRequest->company_name : '') }}"
```

Now both fields are pre-filled, and the admin can edit either field as needed.

## Technical Details

**File Modified:**
- `Modules/Vendor/resources/views/vendors/form.blade.php` (line ~101)

**Change:**
```diff
- value="{{ ... ?? ($language->code == 'en' && $vendorRequest ? $vendorRequest->company_name : '') }}"
+ value="{{ ... ?? ($vendorRequest ? $vendorRequest->company_name : '') }}"
```

## Why This Approach?

The `VendorRequest` model only has a single `company_name` field (not separate English/Arabic fields). The vendor request form captures the company name in the user's preferred language. By pre-filling both fields with this value:

1. **Saves time**: Admin doesn't need to manually copy the name to the Arabic field
2. **Flexible**: Admin can still edit either field to provide proper translations
3. **Consistent**: Both fields start with the same value from the request

## Testing

Test by creating a vendor from a vendor request:
1. Go to vendor requests list
2. Click "Create Vendor" on any pending request
3. Verify both English and Arabic name fields are pre-filled with the company name
4. Both fields should be editable

**Test URL Example:**
```
http://127.0.0.1:8000/en/eg/admin/vendors/create?vendor_request_id=20&email=test@example.com&phone=0109086070&company_name=test
```

## Status
✅ **COMPLETE** - Both English and Arabic name fields now pre-fill from vendor request
