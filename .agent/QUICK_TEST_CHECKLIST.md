# Quick Test Checklist ✅

## Before You Start
- [ ] Queue worker is running: `php artisan queue:work`
- [ ] You're logged in as Admin
- [ ] You have at least 10 products in the database

## Test 1: Command Line Test (Automated)
```bash
php test_import_export.php
```

Expected output:
- [ ] ✅ Export created successfully
- [ ] ✅ First column is 'sku' (not 'id')
- [ ] ✅ No 'id' column found in products sheet
- [ ] ✅ No 'product_id' column found in images/variants sheets
- [ ] ✅ Import validation completed
- [ ] ✅ No import errors (or minimal errors)

## Test 2: Browser Test (Manual)

### Export
1. [ ] Go to: `http://127.0.0.1:8000/en/eg/admin/products`
2. [ ] Click "Export Excel" button
3. [ ] File downloads: `products_export_YYYY-MM-DD_HHMMSS.xlsx`
4. [ ] Open file in Excel/LibreOffice
5. [ ] Verify products sheet first column is `sku`
6. [ ] Verify NO `id` column exists
7. [ ] Verify images sheet has `sku` column (not `product_id`)
8. [ ] Verify variants sheet has `product_sku` column (not `product_id`)

### Import
1. [ ] Go to: `http://127.0.0.1:8000/en/eg/admin/products/bulk-upload`
2. [ ] Click "Choose Excel File"
3. [ ] Select the exported file
4. [ ] Click "Import" button
5. [ ] Progress bar appears and shows "Import in progress..."
6. [ ] Progress updates in real-time (0% → 100%)
7. [ ] After completion, shows: "X succeeded, Y failed out of Z total rows"
8. [ ] Click "View Details" button
9. [ ] Modal opens with results summary
10. [ ] If errors exist, they show with sheet name, row, SKU, and error message
11. [ ] Click "Download Errors CSV" (if errors exist)
12. [ ] CSV downloads with error details

### Verify Data
1. [ ] Go to products list
2. [ ] Products are visible
3. [ ] Check a product detail page
4. [ ] Variants are correct
5. [ ] Images are displayed
6. [ ] Stock quantities are correct

## Test 3: Error Handling

### Test Invalid Data
1. [ ] Open exported Excel file
2. [ ] Delete a SKU value (make it empty)
3. [ ] Save and import
4. [ ] Expected: Error shows "The sku field is required"
5. [ ] Expected: Other rows still import successfully

### Test Duplicate SKU
1. [ ] Open exported Excel file
2. [ ] Copy a row and paste it (duplicate SKU)
3. [ ] Save and import
4. [ ] Expected: Error shows "Duplicate SKU in Excel"
5. [ ] Expected: First occurrence imports, duplicate is skipped

## Test 4: Update Scenario

1. [ ] Export products
2. [ ] Modify a product title in Excel
3. [ ] Modify a variant price in Excel
4. [ ] Save and import
5. [ ] Expected: Products are UPDATED (not duplicated)
6. [ ] Expected: Changes are reflected in database
7. [ ] Expected: No duplicate SKUs created

## Test 5: Large File (Optional)

1. [ ] Export 100+ products (if available)
2. [ ] Import the large file
3. [ ] Expected: Import completes without timeout
4. [ ] Expected: Memory usage stays reasonable
5. [ ] Expected: All products imported correctly

## Common Issues

### ❌ "The id field is required"
**Solution:** You're using an OLD Excel file. Export a FRESH file and import it.

### ❌ Queue worker not processing
**Solution:** Run `php artisan queue:work` in a terminal

### ❌ Import stuck at 0%
**Solution:** 
- Check queue worker terminal for errors
- Check `storage/logs/laravel.log`
- Restart queue worker

### ❌ "Vendor ID is required"
**Solution:** Make sure vendor_id column has values in the Excel file

## Success Criteria

All checkboxes should be checked ✅

If any test fails:
1. Check `storage/logs/laravel.log` for errors
2. Check queue worker terminal output
3. Verify database has proper data (departments, categories, vendors)
4. Ensure you're using a FRESH export (not an old file)

## Final Verification

- [ ] Export works correctly (SKU-based structure)
- [ ] Import works correctly (no validation errors)
- [ ] Batch progress shows real-time updates
- [ ] Results display correctly
- [ ] Error handling works
- [ ] Data is correctly saved to database
- [ ] Updates work (no duplicates)

## If All Tests Pass

🎉 **Congratulations!** The export/import system is working correctly!

You can now:
- Use it in production
- Train users on the new SKU-based system
- Update demo Excel files
- Document the process for your team

## If Tests Fail

1. Review error messages carefully
2. Check the detailed testing guide: `.agent/COMPLETE_TESTING_GUIDE.md`
3. Check the code files mentioned in error messages
4. Verify database integrity
5. Ensure all migrations are run
6. Clear cache: `php artisan cache:clear`
7. Restart queue worker

## Need Help?

Check these files:
- `.agent/COMPLETE_TESTING_GUIDE.md` - Detailed testing instructions
- `.agent/EXCEL_IMPORT_SYNC_SUMMARY.md` - Issue analysis
- `.agent/EXPORT_IMPORT_TEMPLATE_ALIGNMENT_COMPLETE.md` - Code changes summary
- `IMPORT_EXPORT_QUICK_GUIDE.md` - User guide
