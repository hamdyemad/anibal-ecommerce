# Variant Link ID - Quick Reference Card

## The Problem
```
Red (ID: 3) ──┐
              ├──> Small (ID: 5)
Blue (ID: 4) ─┘

When storing "Small", which parent was used? Red or Blue?
```

## The Solution
Store both:
- `variant_configuration_id` = 5 (Small)
- `variant_link_id` = 10 (Red→Small link) or 11 (Blue→Small link)

## Quick API Call

```javascript
// Get link ID
const { link_id } = await fetch(
  `/admin/variants-configurations/get-link-id?parent_id=3&child_id=5`
).then(r => r.json());

// Use in form
variantData.variant_link_id = link_id;
```

## Database
```sql
-- New column in vendor_product_variants
variant_link_id BIGINT UNSIGNED NULL
```

## Model Methods

```php
// Get link ID from parent to child
$parent->getLinkIdToChild($childId);

// Get link ID from child to parent
$child->getLinkIdFromParent($parentId);

// Get the link relationship
$variant->variantLink;
```

## When to Use

✅ **Use variant_link_id when:**
- A variant is linked to multiple parents
- You need to track the exact parent-child path
- Generating reports on variant usage

❌ **Don't use when:**
- Simple products (no variants)
- Direct parent-child (not using linking feature)
- Backward compatibility with old data

## Form Submission

```javascript
{
  configuration_type: 'variants',
  variants: [
    {
      variant_configuration_id: 5,    // Required
      variant_link_id: 10,             // Optional (but recommended)
      price: 200.00,
      sku: 'SKU-123',
      stocks: [...]
    }
  ]
}
```

## Files to Check
- Migration: `2026_03_10_000000_add_variant_link_id_to_vendor_product_variants_table.php`
- Model: `VendorProductVariant.php`
- Link Model: `VariantConfigurationLink.php`
- Repository: `ProductRepository.php` → `handleProductVariants()`
- Controller: `VariantsConfigurationController.php` → `getLinkId()`
- Route: `variants-configurations/get-link-id`

## Testing

```bash
# Run migration
php artisan migrate

# Test API endpoint
curl "http://localhost/admin/variants-configurations/get-link-id?parent_id=3&child_id=5"
```

## Common Scenarios

### Scenario 1: Linked Variant
```
User selects: Red → Small
variant_configuration_id: 5
variant_link_id: 10 ✅
```

### Scenario 2: Simple Product
```
No variants selected
variant_configuration_id: null
variant_link_id: null ✅
```

### Scenario 3: Old Data
```
Existing product (before feature)
variant_configuration_id: 5
variant_link_id: null ✅ (backward compatible)
```
