# Vendor Bank Products Bulk Upload & Export

## Summary
Created a custom bulk upload and export system for vendor bank products that only handles variants and stocks (2 sheets). Vendors can update pricing, discounts, and stock quantities for their bank products without modifying product details.

## Features

### 1. Export Functionality
- **Route:** `/admin/products/vendor-bank/export`
- **Sheets:** 2 sheets only
  - `variants` - Product variants with pricing and discount information
  - `variant_stock` - Stock quantities per region
- **Filtering:** Supports all filters from the main products page
- **Selection:** Can export selected products or all filtered products

### 2. Bulk Upload Functionality
- **Route:** `/admin/products/vendor-bank/bulk-upload`
- **Sheets:** 2 sheets only (same as export)
- **Updates Only:** Cannot create new products, only update existing variants and stocks
- **Validation:** Validates SKUs, ensures products belong to vendor, checks bank product type

### 3. Demo File
- **Route:** `/admin/products/vendor-bank/download-demo`
- **Purpose:** Provides example Excel file with correct structure
- **Sheets:** Contains sample data for both variants and variant_stock sheets

## Files Created

### Routes
**File:** `Modules/CatalogManagement/routes/web.php`
- `products.vendor-bank.export` - Export vendor bank products
- `products.vendor-bank.bulk-upload` - Show bulk upload page
- `products.vendor-bank.bulk-upload.store` - Process upload
- `products.vendor-bank.download-demo` - Download demo file
- `products.vendor-bank.bulk-upload.progress` - Check import progress

### Controller Methods
**File:** `Modules/CatalogManagement/app/Http/Controllers/ProductController.php`
- `vendorBankExport()` - Export vendor bank products
- `vendorBankBulkUpload()` - Show bulk upload page
- `vendorBankBulkUploadStore()` - Process bulk upload
- `vendorBankDownloadDemo()` - Download demo Excel
- `vendorBankCheckImportProgress()` - Check batch progress

### Views
**File:** `Modules/CatalogManagement/resources/views/product/vendor-bank-bulk-upload.blade.php`
- Simplified bulk upload interface
- Progress modal for import tracking
- Error display for failed imports
- Instructions specific to vendor bank products

### Export Classes
1. **VendorBankProductsExport.php** - Main export class
2. **VendorBankVariantsSheetExport.php** - Variants sheet export
3. **VendorBankVariantStockSheetExport.php** - Stock sheet export
4. **VendorBankProductsDemoExport.php** - Demo file export
5. **VendorBankVariantsDemoSheetExport.php** - Demo variants sheet
6. **VendorBankVariantStockDemoSheetExport.php** - Demo stock sheet

### Import Classes
1. **ProcessVendorBankProductImport.php** - Job for processing import
2. **VendorBankProductsImport.php** - Main import class
3. **VendorBankVariantsSheetImport.php** - Variants sheet import
4. **VendorBankVariantStockSheetImport.php** - Stock sheet import

## Excel Structure

### Sheet 1: variants
| Column | Description | Required |
|--------|-------------|----------|
| product_id | Incremental ID | Info only |
| product_sku | Product SKU | Info only |
| product_name_en | Product name (English) | Info only |
| product_name_ar | Product name (Arabic) | Info only |
| sku | Variant SKU | Required |
| variant_configuration_id | Variant config ID | Info only |
| variant_name | Variant name | Info only |
| price | Variant price | Required |
| has_discount | yes/no | Optional |
| price_before_discount | Original price | Optional |
| discount_end_date | Discount end date (Y-m-d) | Optional |
| tax_id | Tax ID | Optional |

### Sheet 2: variant_stock
| Column | Description | Required |
|--------|-------------|----------|
| product_sku | Product SKU | Info only |
| product_name_en | Product name | Info only |
| variant_sku | Variant SKU | Required |
| variant_name | Variant name | Info only |
| region_id | Region ID | Required |
| region_name_en | Region name (English) | Info only |
| region_name_ar | Region name (Arabic) | Info only |
| stock | Stock quantity | Required |

## How It Works

### Export Process
1. Vendor clicks "Export" button on vendor bank products page
2. System filters bank products by vendor's departments
3. Generates Excel file with 2 sheets containing variants and stocks
4. Downloads file with name: `vendor_bank_products_export_YYYY-MM-DD_HHMMSS.xlsx`

### Import Process
1. Vendor navigates to bulk upload page
2. Downloads demo file to understand structure
3. Fills in Excel file with updated data
4. Uploads file
5. System validates:
   - File format (xlsx/xls)
   - Variant SKUs exist and belong to vendor
   - Products are bank type
   - Region IDs are valid
6. Updates variants (pricing, discounts, tax)
7. Updates or creates stock entries per region
8. Shows progress modal during import
9. Displays errors if any validation fails

### Validation Rules

**Variants Sheet:**
- SKU must exist in system
- Variant must belong to vendor
- Product must be bank type
- Price must be numeric and >= 0
- If has_discount = yes, price_before_discount is required

**Variant Stock Sheet:**
- Variant SKU must exist
- Variant must belong to vendor
- Product must be bank type
- Region ID must be valid
- Stock must be integer >= 0

## Security & Permissions

- Only vendors can access these features
- Vendors can only export/import their own bank products
- Products are filtered by vendor's assigned departments
- Cannot modify product details (title, description, etc.)
- Cannot create new products or variants
- Can only update pricing and stock for existing variants

## User Interface

### Vendor Bank Products Page
- Added "Bulk Upload" button in header
- Export functionality integrated with existing export button

### Bulk Upload Page
- Clean, focused interface
- Progress tracking with modal
- Error display with sheet, row, and SKU information
- Instructions specific to vendor bank products
- Demo file download button

## Translations

Added translations in both English and Arabic:
- `vendor_bank_bulk_upload`
- `import_vendor_bank_products`
- `vendor_bank_import_note`
- `vendor_bank_import_description`
- `excel_structure`
- `variants_sheet_description`
- `variant_stock_sheet_description`
- `important_notes`
- `vendor_bank_note_1` through `vendor_bank_note_4`
- And more...

## Technical Notes

- Uses Laravel Excel (Maatwebsite) for import/export
- Batch processing with Laravel Queue for imports
- Progress tracking via batch ID
- Error collection and display
- Chunk reading for large files (100 rows per chunk)
- Batch inserts for performance (100 rows per batch)
- Automatic file cleanup after import
- Session-based error storage for display after redirect

## Benefits

1. **Simplified Interface:** Only 2 sheets instead of 6, focused on what vendors need
2. **Safety:** Cannot accidentally modify product details
3. **Efficiency:** Bulk update pricing and stock for multiple products
4. **Clarity:** Clear instructions and demo file
5. **Validation:** Comprehensive validation prevents errors
6. **Progress Tracking:** Real-time progress updates during import
7. **Error Reporting:** Detailed error messages with row numbers and SKUs
