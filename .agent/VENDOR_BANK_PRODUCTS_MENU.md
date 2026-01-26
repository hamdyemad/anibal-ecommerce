# Vendor Bank Products Menu Implementation

## Summary
Created a new "Available Bank Products" menu item for vendors that displays bank products in their assigned departments with the EXACT same interface, filters, and functionality as the main products index page. Additionally, the regular products pages now exclude bank products for vendors, keeping bank products separate.

## Changes Made

### 1. Routes Added
**File:** `Modules/CatalogManagement/routes/web.php`
- Added `admin.products.vendor-bank` route (GET)
- Added `admin.products.vendor-bank.datatable` route (GET)

### 2. Controller Methods Added
**File:** `Modules/CatalogManagement/app/Http/Controllers/ProductController.php`
- `vendorBankProducts()` - Displays the vendor bank products page with all filter data
- `vendorBankDatatable()` - Returns filtered datatable data using the regular getDataTable action

Both methods:
- Check if user is a vendor
- Get vendor's assigned departments
- Pass `vendor_department_ids` and `product_type=bank` filters to the datatable action
- Provide all necessary data (brands, departments, categories, subcategories, vendors)

### 3. View Created
**File:** `Modules/CatalogManagement/resources/views/product/vendor-bank.blade.php`
- **EXACT CLONE** of `index.blade.php` structure
- Same table headers (without drag handle and vendor column for vendors)
- Same filters from `product_configurations_table/_filters.blade.php`
- Same datatable scripts and handlers
- Same modals
- Points to vendor-specific datatable route
- Shows only bank products in vendor's departments

### 4. Translations Added
**Files:** 
- `lang/en/menu.php`
- `lang/ar/menu.php`
- `Modules/CatalogManagement/lang/en/product.php`
- `Modules/CatalogManagement/lang/ar/product.php`

Added translations:
- `menu.products.vendor_bank_products` (EN: "available bank products", AR: "منتجات البنك المتاحة")
- `catalogmanagement::product.vendor_bank_products` (EN: "Available Bank Products", AR: "منتجات البنك المتاحة")

### 5. Menu Updated
**File:** `resources/views/partials/_menu.blade.php`
- Added new menu item for vendors only (using `@if(isVendor())`)
- Shows count of bank products in vendor's departments
- Positioned after the admin bank products menu
- Added route to parent menu open check
- **Updated all vendor product counts to exclude bank products:**
  - All Products count
  - Pending Products count
  - Rejected Products count
  - Accepted Products count

### 6. ProductAction Enhanced
**File:** `Modules/CatalogManagement/app/Actions/ProductAction.php`

**getDataTable() method:**
- Added support for `vendor_department_ids` filter
- **For vendors: Automatically excludes bank products unless explicitly filtering for them**
- Filters products by department when `vendor_department_ids` parameter is provided
- Updated total records count to exclude bank products for vendors
- Works with VendorProduct queries

**getBankDataTable() method:**
- Enhanced to support explicit `vendor_department_ids` filter
- Maintains backward compatibility with existing vendor filtering
- Applies department filter to both query and total count

## How It Works

### For Vendors:

**Regular Products Pages (`/admin/products`, `/admin/products/pending`, etc.):**
- Now show ONLY regular products (bank products are excluded)
- All filters and search work as before
- Badge counts in menu exclude bank products
- Vendors manage their regular product inventory here

**New "Available Bank Products" Page (`/admin/products/vendor-bank`):**
- Shows ONLY bank products in vendor's departments
- Same interface as regular products page
- All filters available (search, brand, department, category, product type, configuration, active status, stock status, approval status)
- Date range filters
- Per-page selector
- Export functionality
- Bulk upload button (if they have permission)
- Checkbox selection
- All action buttons

### For Admins:
- No changes to existing functionality
- All products pages show all products (including bank products)
- "Bank Products" menu shows all bank products
- No filtering applied

### Product Separation Logic:
1. **Vendor Regular Products:** `product.type != 'bank'`
2. **Vendor Bank Products:** `product.type = 'bank'` AND `product.department_id IN (vendor_departments)`
3. **Admin Products:** All products (no filtering)

## Available Filters (Same as Index Page)

1. **Search** - Search by product name, SKU, brand, category
2. **Brand Filter** - Filter by brand
3. **Department Filter** - Filter by department
4. **Category Filter** - Filter by category (dynamic based on department)
5. **Product Type** - Filter by bank/product type
6. **Configuration** - Filter by simple/variant products
7. **Active Status** - Filter by active/inactive
8. **Stock Status** - Filter by in stock/out of stock
9. **Approval Status** - Filter by pending/approved/rejected
10. **Date Range** - Filter by creation date from/to
11. **Per Page** - Select number of entries to show

## Permissions
Uses existing permission: `products.bank` (type: 'all')
- No new permissions needed
- Both admins and vendors can access bank products
- Filtering is done at the query level based on user type

## Menu Structure
```
Products
├── Stock Setup (Admin only)
├── Bank Products (Admin - all bank products)
├── Available Bank Products (Vendor - bank products in their departments) ← NEW
├── All Products (Vendor - regular products only, Admin - all products)
├── Pending Products (Vendor - regular products only, Admin - all products)
├── Rejected Products (Vendor - regular products only, Admin - all products)
└── Accepted Products (Vendor - regular products only, Admin - all products)
```

## Key Benefits

1. **Clear Separation:** Vendors now have a clear distinction between:
   - Their regular products (managed in regular products pages)
   - Available bank products (viewed in bank products page)

2. **No Confusion:** Bank products don't clutter the regular product listings for vendors

3. **Full Functionality:** Both pages have complete search, filter, and management capabilities

4. **Accurate Counts:** Menu badges show accurate counts excluding bank products from regular product counts

## Technical Notes
- **Complete feature parity** with main products index page for bank products view
- Reuses all existing product datatable components, scripts, filters, and modals
- No duplication of code - uses same includes
- Department filtering happens at database query level for performance
- Badge counts in menu are dynamically calculated and exclude bank products for vendors
- Uses the regular `getDataTable` action with additional filters
- Maintains all search, filter, sort, and pagination functionality
- Bank products are automatically excluded from vendor's regular product queries unless explicitly requested
