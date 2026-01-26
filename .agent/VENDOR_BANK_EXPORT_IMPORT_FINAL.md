# Vendor Bank Products Export/Import - Final Structure

## Summary
Updated the vendor bank products export/import system to have a clean, minimal structure that matches the admin's regular product export format.

## Export Structure

### variants Sheet
Columns:
- `product_sku` - Product SKU for identification (e.g., "3405-hans")
- `sku` - Variant SKU
- `variant_configuration_id` - Variant configuration ID
- `price` - Variant price
- `has_discount` - "yes" or "no"
- `price_before_discount` - Original price if discounted
- `discount_end_date` - Discount end date (YYYY-MM-DD)

**Removed:** `product_id`, `tax_id`

### variant_stock Sheet
Columns:
- `variant_sku` - Variant SKU
- `region_id` - Region ID
- `stock` - Stock quantity

## Import Logic

### Variants Sheet Import
- Validates `product_sku`, `sku`, and `price` as required fields
- Finds variant by matching both `product_sku` and `sku` for the vendor's bank products
- Updates pricing and discount information
- **Does NOT update tax_id** (removed from import logic)

### Variant Stock Sheet Import
- Validates `variant_sku`, `region_id`, and `stock` as required fields
- Finds variant by SKU for the vendor's bank products
- Updates or creates stock records per region

## Key Changes

1. **Removed `product_id` column** - Not needed, `product_sku` is more user-friendly
2. **Removed `tax_id` column** - Vendors cannot modify tax settings
3. **Added `product_sku` for identification** - Vendors can easily identify which product they're editing
4. **Using filter scope** - VendorBankProductsExport now uses the `filter()` scope for cleaner filtering logic
5. **Updated translations** - Removed references to tax information, updated to mention product_sku

## Files Modified

### Export Files
- `Modules/CatalogManagement/app/Exports/VendorBankProductsExport.php` - Uses filter scope
- `Modules/CatalogManagement/app/Exports/VendorBankVariantsSheetExport.php` - Removed tax_id, added product_sku
- `Modules/CatalogManagement/app/Exports/VendorBankVariantStockSheetExport.php` - No changes needed
- `Modules/CatalogManagement/app/Exports/VendorBankVariantsDemoSheetExport.php` - Updated demo data
- `Modules/CatalogManagement/app/Exports/VendorBankVariantStockDemoSheetExport.php` - No changes needed

### Import Files
- `Modules/CatalogManagement/app/Imports/VendorBankVariantsSheetImport.php` - Removed tax_id handling, added product_sku validation
- `Modules/CatalogManagement/app/Imports/VendorBankVariantStockSheetImport.php` - No changes needed

### Translation Files
- `Modules/CatalogManagement/lang/en/product.php` - Updated descriptions
- `Modules/CatalogManagement/lang/ar/product.php` - Updated descriptions

### View Files
- `Modules/CatalogManagement/resources/views/product/vendor-bank.blade.php` - Added `$isVendorBankPage` flag
- `Modules/CatalogManagement/resources/views/product/product_configurations_table/_custom-handlers.blade.php` - Export button uses vendor bank route

## Translation Updates

### English
- `variants_sheet_description`: "Update pricing and discounts for your variants" (removed "and tax information")
- `vendor_bank_note_1`: "...assigned to your departments" (changed from "your vendor")
- `vendor_bank_note_2`: "Use the product_sku and variant SKU to identify..." (added product_sku)

### Arabic
- `variants_sheet_description`: "تحديث الأسعار والخصومات للمتغيرات الخاصة بك" (removed tax reference)
- `vendor_bank_note_1`: "...المخصصة للأقسام الخاصة بك" (changed from "للبائع الخاص بك")
- `vendor_bank_note_2`: "استخدم رمز SKU للمنتج ورمز SKU للمتغير..." (added product_sku)

## Benefits

1. **Cleaner structure** - Matches admin's export format exactly
2. **Better identification** - product_sku makes it easy to identify products
3. **Simplified import** - Vendors only update what they're allowed to (pricing and stock)
4. **Consistent with permissions** - Vendors cannot modify tax settings
5. **Better maintainability** - Uses scope for filtering instead of manual conditions
