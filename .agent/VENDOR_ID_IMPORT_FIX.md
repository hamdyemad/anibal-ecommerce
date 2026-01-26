# Vendor ID Import Fix - Complete

## Issue
When admins exported products and tried to re-import the same Excel file, all rows were failing with "Vendor ID is required" error, even though the `vendor_id` column was present in the exported file.

## Root Cause
The vendor_id validation logic in `ProductsSheetImport` had a critical flaw:

1. `$vendorId` was initialized as `null` at the beginning of the `collection()` method
2. It was only set for vendor users (not admins)
3. The check for admin's `vendor_id` column happened AFTER the initial validation
4. This meant for admins, `$vendorId` remained `null` even when the Excel had valid vendor_id values

### Original Problematic Code:
```php
public function collection(Collection $rows)
{
    $currentUser = Auth::user();
    $vendorId = null;
    
    // Determine vendor_id
    if (isVendor()) {
        $vendorId = $currentUser->vendor?->id;
    }

    foreach ($rows as $index => $row) {
        // ... validation code ...
        
        // For admin: check vendor_id column (TOO LATE!)
        if (isAdmin()) {
            if (isset($row['vendor_id']) && !empty($row['vendor_id'])) {
                $vendorId = (int)$row['vendor_id'];
            }
        }

        if (!$vendorId) {
            // ERROR: Always fails for admins!
            $this->importErrors[] = [...];
            continue;
        }
    }
}
```

## Solution
Moved the vendor_id determination logic INSIDE the foreach loop, so it's evaluated per row BEFORE validation:

### Fixed Code:
```php
public function collection(Collection $rows)
{
    $currentUser = Auth::user();
    
    foreach ($rows as $index => $row) {
        $excelId = (int)($row['id'] ?? 0);
        $sku     = $this->normalizeSku($row['sku'] ?? '');
        
        // Determine vendor_id per row
        $vendorId = null;
        if (isVendor()) {
            $vendorId = $currentUser->vendor?->id;
        } elseif (isAdmin()) {
            // For admin: check vendor_id column in the row
            if (isset($row['vendor_id']) && !empty($row['vendor_id'])) {
                $vendorId = (int)$row['vendor_id'];
            }
        }
        
        // ... rest of validation ...
    }
}
```

## Changes Made

### File: `Modules/CatalogManagement/app/Imports/ProductsSheetImport.php`

1. **Removed global `$vendorId` initialization**: No longer set at method start
2. **Moved vendor_id logic inside loop**: Now evaluated per row before validation
3. **Removed duplicate vendor_id check**: Eliminated the redundant check that was happening after validation

## Impact

### Before Fix:
- ❌ Admin exports fail to re-import
- ❌ All rows show "Vendor ID is required" error
- ❌ Export/import workflow broken for admins

### After Fix:
- ✅ Admin exports can be re-imported successfully
- ✅ vendor_id column is properly read from Excel
- ✅ Export/import workflow works for both admins and vendors
- ✅ Vendors still use their authenticated vendor_id
- ✅ Admins can import products for any vendor

## Testing

### Test Case 1: Admin Export/Import
1. Login as admin
2. Export products from products list
3. Upload the same exported file
4. **Expected**: All products import successfully (or only fail for actual validation errors)

### Test Case 2: Admin Import with Multiple Vendors
1. Login as admin
2. Create Excel with products for different vendors
3. Set different vendor_id values in the vendor_id column
4. Upload the file
5. **Expected**: Products are created/updated for their respective vendors

### Test Case 3: Vendor Import
1. Login as vendor
2. Export products
3. Upload the same file
4. **Expected**: Products import successfully using vendor's authenticated ID

### Test Case 4: Invalid Vendor ID
1. Login as admin
2. Create Excel with non-existent vendor_id (e.g., 99999)
3. Upload the file
4. **Expected**: Shows "Vendor not found" error (not "Vendor ID is required")

## Related Files
- `Modules/CatalogManagement/app/Imports/ProductsSheetImport.php` - Fixed
- `Modules/CatalogManagement/app/Imports/VariantsSheetImport.php` - No changes needed
- `Modules/CatalogManagement/app/Imports/VendorBankProductsImport.php` - Separate flow, no changes needed

## Notes
- This fix only affects the products bulk upload feature
- Vendor bank products import uses a different flow and was not affected
- The fix maintains backward compatibility with existing import files
- Both admin and vendor import workflows continue to work as expected
