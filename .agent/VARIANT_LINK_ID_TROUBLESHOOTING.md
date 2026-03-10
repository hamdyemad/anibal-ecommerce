# Variant Link ID Troubleshooting Guide

## Issue
The `variant_link_id` is not being stored in the database when updating products with variants.

## How It Should Work

1. User selects Color → Red (parent variant)
2. User selects Size → Small (child variant)
3. JavaScript fetches the link ID between Red and Small
4. Link ID is stored in a hidden input field
5. Form submits with the link ID
6. Backend stores it in `vendor_product_variants.variant_link_id`

## Debugging Steps

### Step 1: Check Browser Console
Open the browser console (F12) and look for these messages when selecting a variant:

```
🔗 Fetching variant link ID for parent: 3 child: 5
🔗 Link ID response: {success: true, link_id: 10, ...}
✅ Variant link ID found: 10
✅ Variant link ID stored in form: 10
✅ Hidden input created: <input type="hidden" name="variants[1001][variant_link_id]" value="10">
```

**If you see errors:**
- Check the API endpoint URL
- Check if the route is registered
- Check if parent/child IDs are correct

### Step 2: Inspect Form Before Submit
Before clicking submit, inspect the form HTML:

1. Right-click on the variant box
2. Select "Inspect Element"
3. Look for: `<input type="hidden" name="variants[X][variant_link_id]" value="Y">`

**If the input doesn't exist:**
- The AJAX call failed
- Check console for errors
- Verify the route is accessible

### Step 3: Check Network Tab
1. Open Network tab in browser (F12 → Network)
2. Select a variant (Red → Small)
3. Look for a request to: `/admin/variants-configurations/get-link-id?parent_id=3&child_id=5`
4. Check the response

**Expected response:**
```json
{
  "success": true,
  "link_id": 10,
  "parent_id": 3,
  "child_id": 5
}
```

### Step 4: Check Form Submission
1. Open Network tab
2. Submit the form
3. Find the POST request to `/admin/products/{id}`
4. Check the "Payload" or "Form Data"
5. Look for: `variants[X][variant_link_id]: 10`

**If it's missing:**
- The hidden input wasn't created
- The input name is wrong
- JavaScript error prevented creation

### Step 5: Check Backend Processing
Add logging to `ProductRepository.php` in the `handleProductVariants` method:

```php
Log::info('Variant data received:', [
    'variant_index' => $variantIndex,
    'variant_data' => $variantData,
    'has_link_id' => isset($variantData['variant_link_id']),
    'link_id_value' => $variantData['variant_link_id'] ?? 'not set'
]);
```

Check the Laravel log file: `storage/logs/laravel.log`

## Common Issues

### Issue 1: Route Not Found (404)
**Symptom:** Console shows 404 error when fetching link ID

**Solution:**
```bash
php artisan route:clear
php artisan route:cache
```

### Issue 2: Parent ID Not Found
**Symptom:** Console shows "No link ID found"

**Cause:** The code can't determine the parent variant ID

**Solution:** Check if you have at least 2 levels of selection (parent → child)

### Issue 3: Hidden Input Not Submitted
**Symptom:** Input exists in HTML but not in form data

**Cause:** Input is outside the `<form>` tag

**Solution:** Verify the input is inside `#variant-${variantIndex}` which is inside the form

### Issue 4: Backend Not Saving
**Symptom:** Link ID is in form data but not saved to database

**Check:**
1. Is `variant_link_id` in the `$updateData` or `$createData` array?
2. Is the column nullable in the database?
3. Check Laravel logs for errors

## Manual Testing

### Test 1: API Endpoint
```bash
curl "http://127.0.0.1:8000/en/eg/admin/variants-configurations/get-link-id?parent_id=3&child_id=5"
```

Expected response:
```json
{"success":true,"link_id":10,"parent_id":3,"child_id":5}
```

### Test 2: Database Query
```sql
-- Check if links exist
SELECT * FROM variants_configurations_links 
WHERE parent_config_id = 3 AND child_config_id = 5;

-- Check if variant was saved with link
SELECT id, variant_configuration_id, variant_link_id 
FROM vendor_product_variants 
WHERE variant_configuration_id = 5;
```

### Test 3: Form Inspection
1. Add a variant
2. Select Color → Red
3. Select Size → Small
4. Open console and run:
```javascript
console.log($('input[name*="variant_link_id"]').length);
console.log($('input[name*="variant_link_id"]').val());
```

Should show: `1` and the link ID value

## Quick Fix Checklist

- [ ] Route exists: `php artisan route:list | grep get-link-id`
- [ ] API returns link ID: Test in browser or Postman
- [ ] Console shows "✅ Variant link ID stored"
- [ ] Hidden input exists in HTML before submit
- [ ] Form data includes `variant_link_id` in Network tab
- [ ] Backend receives the link ID (check logs)
- [ ] Database column exists and is nullable
- [ ] Repository code includes link ID in update/create

## Files to Check

1. **Route:** `Modules/CatalogManagement/routes/web.php`
2. **Controller:** `Modules/CatalogManagement/app/Http/Controllers/VariantsConfigurationController.php` → `getLinkId()`
3. **Frontend:** `Modules/CatalogManagement/resources/views/product/edit.blade.php` → `fetchAndStoreVariantLinkId()`
4. **Repository:** `Modules/CatalogManagement/app/Repositories/ProductRepository.php` → `handleProductVariants()`
5. **Migration:** `Modules/CatalogManagement/database/migrations/2026_03_10_000000_add_variant_link_id_to_vendor_product_variants_table.php`
