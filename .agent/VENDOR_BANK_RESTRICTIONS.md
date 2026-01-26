# Vendor Bank Products Restrictions

## Summary
Added restrictions to prevent vendors from editing or deleting bank products. Vendors can only view bank products and update their variants/stocks through the bulk upload feature. Also removed the Product Type filter from the vendor bank products page since all products are bank type.

## Changes Made

### 1. Hidden Product Type Filter
**File:** `Modules/CatalogManagement/resources/views/product/vendor-bank.blade.php`
- Added `$hideProductTypeFilter = true` flag
- Removed `product_type` from `$customSelectIds` array
- Removed `productType` from `$filterOptions` array

**File:** `Modules/CatalogManagement/resources/views/product/product_configurations_table/_filters.blade.php`
- Added conditional check `@if(!isset($hideProductTypeFilter) || !$hideProductTypeFilter)`
- Product Type filter only shows when flag is not set or is false

### 2. Restricted Edit Button
**File:** `Modules/CatalogManagement/resources/views/product/product_configurations_table/_datatable-scripts.blade.php`
- Added condition: `if (row.product_type !== 'bank' || {{ isAdmin() ? 'true' : 'false' }})`
- Edit button only shows for:
  - Regular products (non-bank) for all users
  - Bank products for admins only

### 3. Restricted Delete Button
**File:** `Modules/CatalogManagement/resources/views/product/product_configurations_table/_datatable-scripts.blade.php`
- Added condition: `if (row.product_type !== 'bank' || {{ isAdmin() ? 'true' : 'false' }})`
- Delete button only shows for:
  - Regular products (non-bank) for all users
  - Bank products for admins only

### 4. Server-Side Edit Validation
**File:** `Modules/CatalogManagement/app/Http/Controllers/ProductController.php`
**Method:** `edit()`
- Added check for vendors trying to edit bank products
- Returns 403 error with message: "Vendors cannot edit bank products"
- Prevents direct URL access to edit page

```php
// Vendors cannot edit bank products
if($product->product && $product->product->type === 'bank') {
    return abort(403, __('catalogmanagement::product.cannot_edit_bank_product'));
}
```

### 5. Server-Side Delete Validation
**File:** `Modules/CatalogManagement/app/Http/Controllers/ProductController.php`
**Method:** `destroy()`
- Added check for vendors trying to delete bank products
- Returns 403 error with message: "Vendors cannot delete bank products"
- Prevents API calls to delete endpoint

```php
// Vendors cannot delete bank products
if($product->product && $product->product->type === 'bank') {
    return response()->json([
        'success' => false,
        'message' => __('catalogmanagement::product.cannot_delete_bank_product')
    ], 403);
}
```

### 6. Translations Added
**Files:** 
- `Modules/CatalogManagement/lang/en/product.php`
- `Modules/CatalogManagement/lang/ar/product.php`

**English:**
- `cannot_edit_bank_product` - "Vendors cannot edit bank products. You can only update variants and stocks through bulk upload."
- `cannot_delete_bank_product` - "Vendors cannot delete bank products. Please contact administration."

**Arabic:**
- `cannot_edit_bank_product` - "لا يمكن للبائعين تعديل منتجات البنك. يمكنك فقط تحديث المتغيرات والمخزون من خلال الرفع الجماعي."
- `cannot_delete_bank_product` - "لا يمكن للبائعين حذف منتجات البنك. يرجى الاتصال بالإدارة."

## Vendor Capabilities for Bank Products

### What Vendors CAN Do:
✅ View bank products in their departments
✅ View product details
✅ Manage stock and pricing (stock management page)
✅ Export bank products (variants and stocks)
✅ Bulk upload to update variants and stocks
✅ Change activation status (if they have permission)

### What Vendors CANNOT Do:
❌ Edit bank product details (title, description, images, etc.)
❌ Delete bank products
❌ Move bank products
❌ Change product type
❌ Modify product structure

## Admin Capabilities for Bank Products

### What Admins CAN Do:
✅ Everything vendors can do, PLUS:
✅ Edit bank product details
✅ Delete bank products
✅ Move products to/from bank
✅ Change product type
✅ Full product management

## Security Layers

### 1. UI Layer (Frontend)
- Edit and delete buttons hidden for vendors on bank products
- Product type filter hidden on vendor bank products page

### 2. Server Layer (Backend)
- Edit method validates product type and user role
- Delete method validates product type and user role
- Returns appropriate error messages
- Prevents direct URL/API access

### 3. Permission Layer
- Existing permission system still applies
- Additional bank product checks on top of permissions

## User Experience

### For Vendors:
1. **Viewing Bank Products:**
   - Can see all bank products in their departments
   - Product type filter is hidden (all are bank products)
   - Edit and delete buttons don't appear

2. **Attempting to Edit/Delete:**
   - If they try direct URL access, they get a 403 error
   - Clear error message explains they can't edit/delete bank products
   - Directed to use bulk upload for updates

3. **Managing Variants/Stocks:**
   - Use the bulk upload feature
   - Download current data via export
   - Update pricing, discounts, and stock quantities
   - Upload the modified Excel file

### For Admins:
1. **Full Control:**
   - All buttons visible for bank products
   - Can edit, delete, and manage as usual
   - No restrictions

## Benefits

1. **Data Integrity:** Bank products remain consistent across all vendors
2. **Clear Boundaries:** Vendors know what they can and cannot modify
3. **Guided Workflow:** Vendors directed to proper tools (bulk upload) for updates
4. **Security:** Multiple layers prevent unauthorized modifications
5. **User-Friendly:** Clear error messages explain restrictions
6. **Flexible:** Admins retain full control when needed

## Technical Notes

- Product type check: `$product->product->type === 'bank'`
- User role check: `isAdmin()` helper function
- Blade directive: `{{ isAdmin() ? 'true' : 'false' }}` for JavaScript
- Error codes: 403 (Forbidden) for unauthorized actions
- Validation happens at both UI and server levels
- Existing permissions system remains intact
