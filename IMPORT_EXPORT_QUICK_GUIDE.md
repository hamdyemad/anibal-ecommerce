# Import/Export Quick Reference Guide

## Quick Start

### Export Products
1. Go to Products page
2. Click "Export Excel" button
3. File downloads automatically

### Import Products
1. Go to Bulk Upload page
2. Click "Download Demo Excel" (optional)
3. Fill in your data
4. Upload file
5. Wait for completion
6. View results

## Excel File Structure

### Products Sheet
```
sku | vendor_id* | title_en | title_ar | department | main_category | ...
```
*Required for admin only

### Images Sheet
```
sku | image | is_main
```

### Variants Sheet
```
product_sku | sku | variant_configuration_id | price | ...
```

### Variant Stock Sheet
```
sku | region_id | stock
```

## Required Fields

### Products
- ✅ `sku`
- ✅ `vendor_id` (admin only)
- ✅ `department`
- ✅ `main_category`

### Images
- ✅ `sku`
- ✅ `image`

### Variants
- ✅ `product_sku`
- ✅ `sku`
- ✅ `price`

### Variant Stock
- ✅ `sku`
- ✅ `region_id`
- ✅ `stock`

## Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| Vendor ID is required | Empty vendor_id column | Fill in vendor_id for all rows |
| Product not found | SKU doesn't exist | Check SKU matches products sheet |
| Variant not found | Variant SKU doesn't exist | Check variant SKU matches variants sheet |
| Invalid department | Department ID doesn't exist | Use valid department ID |
| Duplicate SKU | SKU already exists | Use unique SKUs |

## Tips

✅ **DO:**
- Use demo template as starting point
- Export before making changes
- Test with small files first
- Check all required fields
- Use unique SKUs
- Verify image URLs work

❌ **DON'T:**
- Don't use duplicate SKUs
- Don't leave required fields empty
- Don't use invalid IDs
- Don't refresh page during import
- Don't upload files > 10MB

## File Limits

- **Max File Size**: 10MB
- **Max Rows**: Unlimited (chunked processing)
- **Formats**: .xlsx, .xls
- **Processing**: 100 rows per chunk

## Getting Help

1. Check error messages in modal
2. Download error CSV
3. Review documentation
4. Check Laravel logs
5. Contact support

## Quick Links

- **Bulk Upload**: `/admin/products/bulk-upload`
- **Download Demo**: Click button on bulk upload page
- **Export**: Click "Export Excel" on products page
- **Documentation**: See `.agent/COMPLETE_IMPORT_EXPORT_DOCUMENTATION.md`
