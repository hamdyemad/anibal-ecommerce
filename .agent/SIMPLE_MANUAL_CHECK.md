# Simple Manual Check - No Scripts Needed

Forget the scripts! Here's the simplest way to verify and fix:

## Step 1: Export from Browser

1. Go to: `http://127.0.0.1:8000/en/eg/admin/products`
2. Click **"Export Excel"** button
3. Download the file (e.g., `products_export_2026-01-26_HHMMSS.xlsx`)

## Step 2: Open the Excel File

Open the downloaded file in Excel or LibreOffice.

## Step 3: Check the Structure

### Products Sheet:
Look at the first row (headers):

**✅ CORRECT (New Structure):**
```
A: sku
B: vendor_id
C: title_en
D: title_ar
...
```

**❌ WRONG (Old Structure):**
```
A: id
B: sku
C: vendor_id
...
```

### Images Sheet:
Look at the first row (headers):

**✅ CORRECT (New Structure):**
```
A: sku
B: image
C: is_main
```

**❌ WRONG (Old Structure):**
```
A: product_id
B: image
C: is_main
```

### Variants Sheet:
Look at the first row (headers):

**✅ CORRECT (New Structure):**
```
A: product_sku
B: sku
C: price
...
```

**❌ WRONG (Old Structure):**
```
A: product_id
B: sku
C: price
...
```

## Step 4: What to Do Based on Results

### If Structure is CORRECT ✅
Great! Import this file:
1. Go to: `http://127.0.0.1:8000/en/eg/admin/products/bulk-upload`
2. Upload this file
3. Click "Import"
4. It will work!

### If Structure is WRONG ❌
The export code is cached. Clear cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

Then export again and check.

## Step 5: Import the Correct File

Once you have a file with the CORRECT structure:
1. Go to bulk upload page
2. Upload the file
3. Import
4. ✅ Success!

## Quick Visual Check

Open your Excel file and look at cell A1 in each sheet:

- **Products sheet A1:** Should say `sku` (not `id`)
- **Images sheet A1:** Should say `sku` (not `product_id`)
- **Variants sheet A1:** Should say `product_sku` (not `product_id`)

If all three are correct, you're good to go!

## Still Seeing Errors?

If you export a fresh file with the CORRECT structure and STILL get import errors, then:

1. Make sure you're uploading the NEW file (not an old one)
2. Clear browser cache (Ctrl+Shift+Delete)
3. Check the file name to confirm it's the new export
4. Try importing in an incognito/private browser window

## The Real Issue

The file you imported earlier (`products_export_2026-01-26_123841.xlsx`) was exported BEFORE our code changes. That's why it has the old structure.

Export a NEW file NOW and it will have the correct structure.

## Bottom Line

1. Export from browser RIGHT NOW
2. Open the file
3. Check cell A1 in products sheet = `sku`? ✅
4. Check cell A1 in images sheet = `sku`? ✅
5. Check cell A1 in variants sheet = `product_sku`? ✅
6. If all YES → Import it!
7. If any NO → Clear cache and export again

That's it! No scripts needed. Just visual check. 👀
