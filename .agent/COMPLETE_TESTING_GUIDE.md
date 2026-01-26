# Complete Testing Guide - Export/Import with Batches

## Prerequisites
- Laravel application running
- Queue worker running (`php artisan queue:work`)
- Database with at least 10 products
- Admin user logged in

## Test Scenario: Export 10 Products and Import with Batches

### Phase 1: Prepare Test Data (If Needed)

If you don't have 10 products, create them first:

```bash
# Run seeder to create test products
php artisan db:seed --class=AutoProductSeeder
```

Or create manually through the admin panel.

### Phase 2: Export Products

#### Step 1: Navigate to Products Page
1. Login as Admin
2. Go to: `http://127.0.0.1:8000/en/eg/admin/products`
3. You should see a list of products

#### Step 2: Export Excel File
1. Click the **"Export Excel"** button (top right)
2. Wait for download to complete
3. File name will be: `products_export_2026-01-26_HHMMSS.xlsx`
4. Save the file to your computer

#### Step 3: Verify Export Structure
Open the Excel file and verify:

**Products Sheet:**
- ✅ First column is `sku` (NOT `id`)
- ✅ Second column is `vendor_id` (for admin)
- ✅ Has columns: title_en, title_ar, department, main_category, etc.
- ✅ Has 10 rows of data (plus header row)

**Images Sheet:**
- ✅ First column is `sku` (NOT `product_id`)
- ✅ Second column is `image`
- ✅ Third column is `is_main`

**Variants Sheet:**
- ✅ First column is `product_sku` (NOT `product_id`)
- ✅ Second column is `sku` (variant SKU)
- ✅ Has columns: price, variant_configuration_id, has_discount, etc.

**Variant_Stock Sheet:**
- ✅ First column is `variant_sku`
- ✅ Has columns: region_id, stock

### Phase 3: Import Products with Batches

#### Step 1: Start Queue Worker
Open a terminal and run:
```bash
php artisan queue:work --tries=3 --timeout=300
```

Keep this terminal open during the import.

#### Step 2: Navigate to Bulk Upload Page
1. Go to: `http://127.0.0.1:8000/en/eg/admin/products/bulk-upload`
2. You should see the upload form

#### Step 3: Upload Excel File
1. Click **"Choose Excel File"** button
2. Select the exported file from Step 2
3. Click **"Import"** button
4. You should see:
   - ✅ Button changes to "Uploading..."
   - ✅ Button becomes disabled
   - ✅ Progress bar appears

#### Step 4: Monitor Batch Progress
Watch the progress indicator:
- ✅ Shows "Import in progress..."
- ✅ Shows progress percentage (0% → 100%)
- ✅ Shows "Processing..." status
- ✅ Updates in real-time (every 2 seconds)

In the queue worker terminal, you should see:
```
[2026-01-26 12:00:00] Processing: Modules\CatalogManagement\app\Jobs\ProcessProductImport
[2026-01-26 12:00:05] Processed:  Modules\CatalogManagement\app\Jobs\ProcessProductImport
```

#### Step 5: Verify Completion
After import completes (should take 5-15 seconds):
- ✅ Progress shows 100%
- ✅ Status changes to "Completed"
- ✅ Shows success message: "X succeeded, Y failed out of Z total rows"
- ✅ Shows "View Details" button
- ✅ Shows "Download Errors CSV" button (if there are errors)

#### Step 6: Check Results Summary
Click **"View Details"** button:
- ✅ Modal opens with detailed results
- ✅ Shows summary: "X products imported successfully"
- ✅ If errors exist, shows error table with:
  - Sheet name (color-coded badge)
  - Row number
  - SKU
  - Error messages

### Phase 4: Verify Data in Database

#### Check Products Table
```sql
SELECT id, slug, configuration_type, department_id, category_id, brand_id, created_at
FROM products
ORDER BY id DESC
LIMIT 10;
```

Expected:
- ✅ 10 products exist (or updated if they already existed)
- ✅ All have proper department_id, category_id
- ✅ configuration_type is 'variants' or 'simple'

#### Check Vendor Products Table
```sql
SELECT id, vendor_id, product_id, sku, max_per_order, is_active, is_featured, status
FROM vendor_products
ORDER BY id DESC
LIMIT 10;
```

Expected:
- ✅ 10 vendor products exist
- ✅ All have proper vendor_id
- ✅ All have unique SKU
- ✅ status is 'approved' (for admin imports)

#### Check Variants Table
```sql
SELECT id, vendor_product_id, sku, price, has_discount, price_before_discount
FROM vendor_product_variants
WHERE vendor_product_id IN (SELECT id FROM vendor_products ORDER BY id DESC LIMIT 10)
ORDER BY id DESC;
```

Expected:
- ✅ Variants exist for products with have_varient = yes
- ✅ All have unique SKU
- ✅ All have price > 0
- ✅ Discount fields populated correctly

#### Check Variant Stock Table
```sql
SELECT id, vendor_product_variant_id, region_id, quantity
FROM vendor_product_variant_stocks
WHERE vendor_product_variant_id IN (
    SELECT id FROM vendor_product_variants 
    WHERE vendor_product_id IN (SELECT id FROM vendor_products ORDER BY id DESC LIMIT 10)
)
ORDER BY id DESC;
```

Expected:
- ✅ Stock entries exist for each variant
- ✅ All have proper region_id
- ✅ quantity values are correct

#### Check Images/Attachments Table
```sql
SELECT id, attachable_id, attachable_type, type, path
FROM attachments
WHERE attachable_type = 'Modules\\CatalogManagement\\app\\Models\\Product'
AND attachable_id IN (SELECT id FROM products ORDER BY id DESC LIMIT 10)
ORDER BY id DESC;
```

Expected:
- ✅ Images exist for products
- ✅ type is 'main_image' or 'additional_image'
- ✅ path points to valid image file

### Phase 5: Test Chunked Processing

#### Modify Chunk Size (Optional)
To test chunking more visibly, temporarily reduce chunk size:

Edit `Modules/CatalogManagement/app/Imports/ProductsSheetImport.php`:
```php
public function chunkSize(): int
{
    return 2; // Process 2 rows at a time instead of 100
}
```

Then repeat the import test. You should see:
- ✅ Multiple chunk processing logs in queue worker
- ✅ Import still completes successfully
- ✅ All data imported correctly

**Remember to change it back to 100 after testing!**

### Phase 6: Test Error Handling

#### Test 1: Invalid SKU
1. Open the exported Excel file
2. Change a SKU to empty string in products sheet
3. Save and import
4. Expected result:
   - ✅ Import completes with errors
   - ✅ Error shows: "The sku field is required"
   - ✅ Shows row number and sheet name
   - ✅ Other valid rows still imported

#### Test 2: Invalid Department
1. Open the exported Excel file
2. Change department to 99999 (non-existent)
3. Save and import
4. Expected result:
   - ✅ Import completes with errors
   - ✅ Error shows: "The selected department is invalid"
   - ✅ Shows row number and SKU

#### Test 3: Missing Vendor ID (Admin Only)
1. Open the exported Excel file
2. Remove vendor_id from a row
3. Save and import
4. Expected result:
   - ✅ Import completes with errors
   - ✅ Error shows: "Vendor ID is required"
   - ✅ Shows row number and SKU

#### Test 4: Duplicate SKU in Excel
1. Open the exported Excel file
2. Copy a row and paste it (duplicate SKU)
3. Save and import
4. Expected result:
   - ✅ Import completes with errors
   - ✅ Error shows: "Duplicate SKU in Excel at row X"
   - ✅ First occurrence imports, duplicate is skipped

### Phase 7: Test Update Scenario

#### Test Updating Existing Products
1. Export products (get current data)
2. Modify some values:
   - Change title_en for a product
   - Change price for a variant
   - Change stock quantity
3. Import the modified file
4. Expected result:
   - ✅ Products are UPDATED (not duplicated)
   - ✅ Changes are reflected in database
   - ✅ No duplicate SKUs created
   - ✅ Activity log shows "updated" action

### Phase 8: Test Large File (Stress Test)

#### Create Large Export
1. Create or seed 1000+ products
2. Export all products
3. Import the large file
4. Expected result:
   - ✅ Import completes without timeout
   - ✅ Memory usage stays reasonable
   - ✅ All products imported correctly
   - ✅ Chunking processes 100 rows at a time

### Phase 9: Test Cache Persistence

#### Test Results Cache
1. Import a file
2. Wait for completion
3. Refresh the page
4. Expected result:
   - ✅ Results still visible (not cleared)
   - ✅ Can still view details
   - ✅ Can still download errors CSV
   - ✅ Results persist for 24 hours

#### Test Multiple Imports
1. Import file A
2. Wait for completion
3. Import file B (different file)
4. Expected result:
   - ✅ File A results replaced by File B results
   - ✅ No confusion between batches
   - ✅ Each import tracked separately

### Phase 10: Test Browser Scenarios

#### Test Page Refresh During Import
1. Start an import
2. Refresh the page while importing
3. Expected result:
   - ✅ Progress resumes automatically
   - ✅ Shows current progress
   - ✅ Completes successfully

#### Test Browser Close/Reopen
1. Start an import
2. Close browser tab
3. Reopen and navigate to bulk upload page
4. Expected result:
   - ✅ Progress resumes automatically
   - ✅ Shows current progress
   - ✅ Completes successfully

#### Test Multiple Tabs
1. Open bulk upload page in 2 tabs
2. Start import in tab 1
3. Check tab 2
4. Expected result:
   - ✅ Both tabs show same progress
   - ✅ No duplicate imports
   - ✅ Results visible in both tabs

## Common Issues and Solutions

### Issue 1: "The id field is required"
**Cause:** Using old Excel file exported before code changes
**Solution:** Export a fresh file and import it

### Issue 2: Queue worker not processing
**Cause:** Queue worker not running
**Solution:** Run `php artisan queue:work` in terminal

### Issue 3: Import stuck at 0%
**Cause:** Job failed or queue worker crashed
**Solution:** 
- Check queue worker terminal for errors
- Check `storage/logs/laravel.log`
- Restart queue worker

### Issue 4: Memory limit exceeded
**Cause:** File too large or chunk size too big
**Solution:**
- Increase PHP memory limit in `php.ini`
- Or reduce chunk size in import classes

### Issue 5: Timeout errors
**Cause:** Import taking too long
**Solution:**
- Ensure queue worker is running (imports run in background)
- Increase timeout in queue worker: `--timeout=600`

## Success Criteria

All tests should pass with these results:

✅ Export generates correct file structure (SKU-based)
✅ Import processes file in chunks (100 rows per chunk)
✅ Batch progress shows real-time updates
✅ Results display correctly (success/failure counts)
✅ Error details show in modal with proper formatting
✅ CSV export of errors works
✅ Data correctly saved to database
✅ Updates work (no duplicates)
✅ Large files process without timeout/memory issues
✅ Cache persists results for 24 hours
✅ Page refresh resumes progress
✅ Error handling works correctly

## Performance Benchmarks

Expected performance:
- **10 products:** 5-10 seconds
- **100 products:** 15-30 seconds
- **1000 products:** 2-5 minutes
- **10000 products:** 20-50 minutes

Memory usage:
- **Small files (<100 rows):** ~50MB
- **Medium files (100-1000 rows):** ~100MB
- **Large files (1000-10000 rows):** ~200MB

## Conclusion

If all tests pass, the export/import system is working correctly with:
- ✅ SKU-based identification
- ✅ Chunked processing
- ✅ Batch job tracking
- ✅ Real-time progress updates
- ✅ Detailed error reporting
- ✅ CSV error export
- ✅ Cache persistence
- ✅ Update capability
- ✅ Scalability for large files

The system is production-ready! 🎉
