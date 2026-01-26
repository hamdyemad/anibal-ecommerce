# Complete Import/Export Documentation

## Overview

The product bulk import/export system allows you to manage products, variants, images, and stock through Excel files. The system uses **SKU-based identification** for easy product management.

## Key Features

1. **SKU-Based System**: All products are identified by SKU instead of internal IDs
2. **Chunked Processing**: Handles large files (10,000+ rows) efficiently
3. **Real-Time Progress**: Shows import progress with detailed results
4. **Error Reporting**: Detailed error messages with row numbers and SKUs
5. **Multi-Sheet Support**: Products, Images, Variants, Variant Stock, Occasions
6. **Role-Based**: Different features for Admin vs Vendor users

## Export System

### How to Export

1. Navigate to Products page
2. Click "Export Excel" button
3. Excel file downloads with all product data

### Export Structure

#### Admin Export
- **products** sheet: All product details with vendor_id
- **images** sheet: Product images with SKUs
- **variants** sheet: Product variants with parent SKUs
- **variant_stock** sheet: Stock levels by region
- **occasions** sheet: Special occasions (optional)
- **occasion_products** sheet: Products in occasions (optional)

#### Vendor Export
- **products** sheet: Vendor's products only (no vendor_id column)
- **images** sheet: Product images
- **variants** sheet: Product variants
- **variant_stock** sheet: Stock levels

### Column Structure

#### Products Sheet
```
sku | vendor_id* | title_en | title_ar | description_en | description_ar | ...
```
*vendor_id only for admin exports

#### Images Sheet
```
sku | image | is_main
```

#### Variants Sheet
```
product_sku | sku | variant_configuration_id | price | has_discount | price_before_discount | discount_end_date
```

#### Variant Stock Sheet
```
sku | region_id | stock
```

## Import System

### How to Import

1. Navigate to Bulk Upload page
2. Download demo Excel template (optional)
3. Fill in your data or use exported file
4. Upload the Excel file
5. Monitor progress in real-time
6. View results (success/failure rows)

### Import Process

1. **File Upload**: User uploads Excel file
2. **Validation**: System validates all data
3. **Chunked Processing**: Processes 100 rows at a time
4. **Progress Tracking**: Real-time progress updates
5. **Results Display**: Shows success count and detailed errors
6. **Error Export**: Download failed rows as CSV

### Required Columns

#### Products Sheet
- `sku` (Required): Unique product identifier
- `vendor_id` (Required for admin): Vendor ID
- `department` (Required): Department ID
- `main_category` (Required): Category ID

#### Images Sheet
- `sku` (Required): Product SKU
- `image` (Required): Image URL

#### Variants Sheet
- `product_sku` (Required): Parent product SKU
- `sku` (Required): Variant SKU
- `price` (Required): Variant price

#### Variant Stock Sheet
- `sku` (Required): Variant SKU
- `region_id` (Required): Region ID
- `stock` (Required): Stock quantity

### Data Validation

#### SKU Validation
- Must be unique
- Cannot be empty
- Max 255 characters
- Normalized (trimmed, lowercase)

#### Price Validation
- Must be numeric
- Must be >= 0
- Required for variants

#### Stock Validation
- Must be integer
- Must be >= 0

#### Relationship Validation
- Department must exist
- Category must belong to department
- Sub-category must belong to category
- Vendor must exist (admin only)
- Product SKU must exist (for images/variants)
- Variant SKU must exist (for stock)

### Error Handling

#### Error Display
- Sheet name (color-coded badge)
- Row number
- SKU/ID
- Detailed error messages

#### Error Export
- Download as CSV
- Contains all failed rows
- Includes error messages

#### Common Errors
1. **Vendor ID is required**: Missing or empty vendor_id (admin only)
2. **Product not found**: SKU doesn't exist in products sheet
3. **Variant not found**: Variant SKU doesn't exist
4. **Invalid department**: Department ID doesn't exist
5. **Category mismatch**: Category doesn't belong to department
6. **Duplicate SKU**: SKU already exists in file or database

## Best Practices

### For Exporting

1. **Export Before Import**: Always export current data before making changes
2. **Backup**: Keep a backup of exported files
3. **Version Control**: Name files with dates (e.g., products_2026-01-26.xlsx)

### For Importing

1. **Use Demo Template**: Start with the demo template for structure
2. **Small Batches**: Test with small files first (10-20 products)
3. **Validate Data**: Check all required fields before uploading
4. **Check Relationships**: Ensure departments, categories exist
5. **Unique SKUs**: Verify all SKUs are unique
6. **Image URLs**: Test image URLs are accessible
7. **Review Errors**: Fix all errors before re-importing

### For Large Files

1. **Chunk Size**: System processes 100 rows at a time
2. **Memory**: Can handle 10,000+ rows without issues
3. **Timeout**: No timeout issues with chunked processing
4. **Progress**: Monitor progress bar for status

## Troubleshooting

### Import Fails Immediately

**Possible Causes:**
- Invalid file format (must be .xlsx or .xls)
- File too large (max 10MB)
- Corrupted file

**Solutions:**
- Check file format
- Reduce file size
- Re-export and try again

### All Rows Fail with "Vendor ID Required"

**Cause:** vendor_id column is empty or missing (admin only)

**Solution:**
- Open Excel file
- Check column B has header "vendor_id"
- Fill in vendor_id for all rows
- Re-upload

### "Product Not Found" Errors

**Cause:** SKU in images/variants sheet doesn't match products sheet

**Solution:**
- Verify SKUs match exactly (case-sensitive)
- Check for typos
- Ensure products sheet imported successfully first

### Images Not Importing

**Possible Causes:**
- Invalid image URLs
- Images not accessible
- Network issues

**Solutions:**
- Test image URLs in browser
- Use publicly accessible URLs
- Check image format (jpg, png, gif)

### Slow Import

**Cause:** Large file or many images

**Solution:**
- This is normal for large files
- System processes in chunks
- Wait for completion
- Don't refresh page

## Advanced Features

### Occasions (Admin Only)

Import special occasions and link products:

1. **occasions** sheet: Define occasions
2. **occasion_products** sheet: Link products to occasions

### Bulk Updates

Update existing products:

1. Export current data
2. Modify values in Excel
3. Re-import
4. System updates existing products by SKU

### Stock Management

Update stock levels:

1. Export current data
2. Modify stock in variant_stock sheet
3. Re-import
4. Stock levels updated

## API Endpoints

### Export
```
GET /admin/products/export
```

### Import
```
POST /admin/products/bulk-upload
```

### Progress Check
```
GET /admin/products/bulk-upload/progress/{batchId}
```

### Download Demo
```
GET /admin/products/download-demo
```

## Technical Details

### File Processing
- Uses Laravel Excel (Maatwebsite)
- Implements WithChunkReading (100 rows/chunk)
- Batch job processing
- Cache-based results storage

### Memory Management
- Chunked reading reduces memory usage
- Processes 100 rows at a time
- Releases memory between chunks
- Can handle unlimited file size

### Progress Tracking
- Real-time progress updates
- Polls every 2 seconds
- Shows percentage complete
- Displays jobs remaining

### Results Caching
- Results cached for 24 hours
- Accessible across page refreshes
- Cleared after retrieval
- Includes success count and errors

## Security

### Access Control
- Admin: Can import for any vendor
- Vendor: Can only import own products
- Role-based permissions

### Validation
- All data validated before import
- SQL injection prevention
- XSS protection
- File type validation

### Data Integrity
- Transaction-based imports
- Rollback on critical errors
- Duplicate prevention
- Relationship validation

## Performance

### Benchmarks
- Small files (< 100 rows): < 10 seconds
- Medium files (100-1000 rows): 30-60 seconds
- Large files (1000-10000 rows): 2-5 minutes
- Very large files (10000+ rows): 5-15 minutes

### Optimization
- Chunked processing
- Batch inserts
- Eager loading
- Query optimization
- Cache utilization

## Support

### Getting Help
1. Check error messages
2. Review documentation
3. Download error CSV
4. Contact support with:
   - Error messages
   - Excel file
   - Screenshots
   - Batch ID

### Common Questions

**Q: Can I import products without images?**
A: Yes, images sheet is optional.

**Q: Can I update existing products?**
A: Yes, system updates products with matching SKUs.

**Q: What happens to existing images when I re-import?**
A: Existing images are replaced with new ones from Excel.

**Q: Can I import products for multiple vendors at once?**
A: Yes (admin only), use different vendor_id values.

**Q: How do I know which row failed?**
A: Error report shows exact row number and SKU.

**Q: Can I pause and resume import?**
A: No, but you can cancel and restart.

**Q: What's the maximum file size?**
A: 10MB, but can be increased in configuration.

**Q: Do I need to fill all columns?**
A: No, only required columns. Optional columns can be empty.

## Changelog

### Version 2.0 (Current)
- SKU-based system (removed ID columns)
- Chunked processing
- Real-time progress tracking
- Detailed error reporting
- CSV error export
- Improved validation

### Version 1.0
- ID-based system
- Single-batch processing
- Basic error messages
