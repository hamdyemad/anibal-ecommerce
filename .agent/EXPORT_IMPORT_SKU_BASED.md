# Export/Import SKU-Based System - Complete

## Overview
Replaced the product_id based system with SKU-based system in Excel exports and imports. This makes the Excel files more user-friendly and easier to understand.

## Changes Made

### Export Files Updated

#### 1. ProductsSheetExport.php
**Before**:
- Columns: `id`, `sku`, `vendor_id`, ...
- Used incremental product_id

**After**:
- Columns: `sku`, `vendor_id`, ...
- Removed `id` column entirely
- SKU is now the first column and primary identifier

#### 2. ImagesSheetExport.php
**Before**:
- Columns: `product_id`, `image`, `is_main`
- Used product_id to link images to products

**After**:
- Columns: `sku`, `image`, `is_main`
- Uses product SKU to link images to products

#### 3. VariantsSheetExport.php
**Before**:
- Columns: `product_id`, `sku`, `variant_configuration_id`, ...
- Used product_id to link variants to products

**After**:
- Columns: `product_sku`, `sku`, `variant_configuration_id`, ...
- Uses parent product SKU to link variants to products
- Renamed column from `product_id` to `product_sku` for clarity

### Import Files Updated

#### 1. ProductsSheetImport.php
**Changes**:
- Now uses SKU as the key in `$productMap` and `$vendorProductMap`
- Changed from: `$this->productMap[$excelId] = $productId`
- Changed to: `$this->productMap[$sku] = $productId`

#### 2. ImagesSheetImport.php
**Changes**:
- Reads `sku` or `product_sku` column instead of `product_id`
- Looks up products using SKU in the productMap
- Better error messages showing SKU instead of ID

#### 3. VariantsSheetImport.php
**Changes**:
- Reads `product_sku` column to identify parent product
- Reads `sku` column for variant SKU
- Uses SKU to look up vendor product in vendorProductMap
- Renamed variables for clarity:
  - `$excelProductId` → `$productSku`
  - `$sku` → `$variantSku`

## Benefits

### 1. User-Friendly
- Users can easily identify products by SKU (e.g., "28388A00") instead of cryptic IDs (e.g., "1234")
- SKUs are meaningful and recognizable
- Easier to manually edit Excel files

### 2. No ID Mapping Needed
- No need to maintain incremental ID mapping
- No confusion about which ID system is being used
- Direct SKU-to-SKU matching

### 3. Better Error Messages
- Errors now show SKU instead of ID
- Example: "Product with SKU '28388A00' not found" vs "Product with ID '5' not found"

### 4. Consistent Across Sheets
- All sheets now use SKU as the primary identifier
- products sheet: `sku`
- images sheet: `sku` (product SKU)
- variants sheet: `product_sku` (parent) and `sku` (variant)

## Excel File Structure

### Before:
```
products sheet:
| id | sku      | vendor_id | title_en | ...
|----|----------|-----------|----------|
| 1  | 28388A00 | 228       | Product1 |
| 2  | 28388GL0 | 228       | Product2 |

images sheet:
| product_id | image                    | is_main |
|------------|--------------------------|---------|
| 1          | http://example.com/1.jpg | yes     |
| 2          | http://example.com/2.jpg | yes     |

variants sheet:
| product_id | sku          | price | ...
|------------|--------------|-------|
| 1          | 28388A00-RED | 100   |
| 2          | 28388GL0-BLU | 150   |
```

### After:
```
products sheet:
| sku      | vendor_id | title_en | ...
|----------|-----------|----------|
| 28388A00 | 228       | Product1 |
| 28388GL0 | 228       | Product2 |

images sheet:
| sku      | image                    | is_main |
|----------|--------------------------|---------|
| 28388A00 | http://example.com/1.jpg | yes     |
| 28388GL0 | http://example.com/2.jpg | yes     |

variants sheet:
| product_sku | sku          | price | ...
|-------------|--------------|-------|
| 28388A00    | 28388A00-RED | 100   |
| 28388GL0    | 28388GL0-BLU | 150   |
```

## Backward Compatibility

### Import Still Supports Old Format
The import code checks for both old and new column names:
- `product_id` OR `sku` in images sheet
- `product_id` OR `product_sku` in variants sheet

This ensures old Excel files still work during the transition period.

## Testing

### Test Cases:

1. **Export Products**
   - Export products from admin
   - Verify `sku` is first column (no `id` column)
   - Verify images sheet has `sku` column
   - Verify variants sheet has `product_sku` column

2. **Import Products**
   - Import the exported file
   - Verify all products import correctly
   - Verify images are linked correctly
   - Verify variants are linked correctly

3. **Manual Editing**
   - Export products
   - Manually edit SKUs in Excel
   - Import back
   - Verify changes are applied

4. **Error Messages**
   - Import file with invalid SKU
   - Verify error shows SKU, not ID
   - Example: "Product with SKU 'INVALID' not found"

## Migration Notes

### For Users:
- Old Excel files with `product_id` will still work
- New exports will use SKU-based format
- Gradually transition to new format

### For Developers:
- `$productMap` now uses SKU as key, not Excel ID
- `$vendorProductMap` now uses SKU as key, not Excel ID
- Update any code that references these maps

## Future Enhancements

Possible improvements:
1. Add SKU validation (format, uniqueness)
2. Support SKU aliases or alternate SKUs
3. Bulk SKU updates via Excel
4. SKU-based product search in import
