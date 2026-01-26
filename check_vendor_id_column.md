# How to Check vendor_id Column Issue

## Steps to Debug:

1. **Upload the file again** (after my latest code changes)

2. **Check the Laravel log**:
   ```bash
   tail -f storage/logs/laravel.log
   ```
   
   Look for a line like:
   ```
   vendor_id not found in Excel row. Available columns: id, sku, title_en, title_ar, ...
   ```

3. **Check your Excel file**:
   - Open the Excel file
   - Click on cell B1 (the header for column B)
   - Copy the exact text
   - It should be exactly: `vendor_id`

## Common Issues:

1. **Extra spaces**: `vendor_id ` (with trailing space)
2. **Different case**: `Vendor_ID` or `VENDOR_ID`
3. **Missing column**: The column doesn't exist at all
4. **Wrong column**: vendor_id is in a different column (not B)

## Quick Fix:

If the column header is wrong, simply:
1. Open the Excel file
2. Change cell B1 to exactly: `vendor_id` (lowercase, with underscore)
3. Save the file
4. Upload again

The code I added will try multiple variations, but the export should be generating `vendor_id` correctly.
