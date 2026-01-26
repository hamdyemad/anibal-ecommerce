# Update Demo Excel Files - Instructions

## Files to Update

1. `public/assets/admin_products_demo.xlsx`
2. `public/assets/vendor_products_demo.xlsx`

## Changes Needed

### Products Sheet

**Remove Column:**
- `id` (first column)

**Keep/Update Columns:**
- `sku` (now first column)
- `vendor_id` (admin only, second column)
- All other columns remain the same

**New Structure:**
```
Admin: sku | vendor_id | title_en | title_ar | ...
Vendor: sku | title_en | title_ar | ...
```

### Images Sheet

**Change Column:**
- `product_id` → `sku`

**New Structure:**
```
sku | image | is_main
```

**Example:**
```
28388A00 | https://example.com/image1.jpg | yes
28388A00 | https://example.com/image2.jpg | no
28388GL0 | https://example.com/image3.jpg | yes
```

### Variants Sheet

**Change Column:**
- `product_id` → `product_sku`

**New Structure:**
```
product_sku | sku | variant_configuration_id | price | has_discount | price_before_discount | discount_end_date
```

**Example:**
```
28388A00 | 28388A00-RED | 1 | 100 | no | | 
28388A00 | 28388A00-BLUE | 1 | 120 | yes | 150 | 2026-12-31
28388GL0 | 28388GL0-SMALL | 2 | 80 | no | |
```

### Variant_Stock Sheet

**No changes needed** - already uses variant SKU

## Step-by-Step Instructions

### For Admin Demo File (`admin_products_demo.xlsx`):

1. Open `public/assets/admin_products_demo.xlsx`
2. Go to **products** sheet:
   - Delete column A (`id`)
   - Column A is now `sku`
   - Column B is now `vendor_id`
   - Update sample data if needed
3. Go to **images** sheet:
   - Change column A header from `product_id` to `sku`
   - Update sample data to use SKUs instead of IDs
4. Go to **variants** sheet:
   - Change column A header from `product_id` to `product_sku`
   - Update sample data to use parent product SKUs
5. Save the file

### For Vendor Demo File (`vendor_products_demo.xlsx`):

1. Open `public/assets/vendor_products_demo.xlsx`
2. Go to **products** sheet:
   - Delete column A (`id`)
   - Column A is now `sku`
   - Update sample data if needed
3. Go to **images** sheet:
   - Change column A header from `product_id` to `sku`
   - Update sample data to use SKUs instead of IDs
4. Go to **variants** sheet:
   - Change column A header from `product_id` to `product_sku`
   - Update sample data to use parent product SKUs
5. Save the file

## Sample Data

### Products Sheet (Admin)
```
sku       | vendor_id | title_en          | title_ar | department | main_category | ...
28388A00  | 1         | Sample Product 1  | منتج 1   | 1          | 1             | ...
28388GL0  | 1         | Sample Product 2  | منتج 2   | 1          | 1             | ...
```

### Products Sheet (Vendor)
```
sku       | title_en          | title_ar | department | main_category | ...
28388A00  | Sample Product 1  | منتج 1   | 1          | 1             | ...
28388GL0  | Sample Product 2  | منتج 2   | 1          | 1             | ...
```

### Images Sheet
```
sku       | image                                    | is_main
28388A00  | https://via.placeholder.com/800x600/1    | yes
28388A00  | https://via.placeholder.com/800x600/2    | no
28388GL0  | https://via.placeholder.com/800x600/3    | yes
```

### Variants Sheet
```
product_sku | sku          | variant_configuration_id | price | has_discount | price_before_discount | discount_end_date
28388A00    | 28388A00-RED | 1                        | 100   | no           |                       |
28388A00    | 28388A00-BLU | 1                        | 120   | yes          | 150                   | 2026-12-31
28388GL0    | 28388GL0-S   | 2                        | 80    | no           |                       |
28388GL0    | 28388GL0-M   | 2                        | 90    | no           |                       |
```

### Variant_Stock Sheet (No changes)
```
sku          | region_id | stock
28388A00-RED | 1         | 100
28388A00-BLU | 1         | 50
28388GL0-S   | 1         | 200
28388GL0-M   | 1         | 150
```

## Verification

After updating the files:

1. Download the demo file from the bulk upload page
2. Verify the structure matches the new format
3. Try importing the demo file
4. Verify all products, images, and variants import correctly

## Notes

- The `id` column is completely removed - SKU is now the primary identifier
- All references to `product_id` are replaced with `sku` or `product_sku`
- This makes the Excel files more user-friendly and easier to understand
- Users can now easily identify products by their SKU instead of internal IDs
