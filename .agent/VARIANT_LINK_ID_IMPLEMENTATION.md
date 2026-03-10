# Variant Link ID Implementation

## Problem
When storing a product with variants like "Color: Red → Size: Small", the system was only storing the `variant_configuration_id` (the ID of "Small"). However, if "Small" is linked to multiple parent variants (e.g., both "Red" and "Blue"), there was no way to determine which specific parent-child relationship was used.

### Example Scenario
- Red (ID: 3) → Small (ID: 5) [Link ID: 10]
- Blue (ID: 4) → Small (ID: 5) [Link ID: 11]

When storing a variant with `variant_configuration_id = 5`, we couldn't tell if it came from the Red→Small path or Blue→Small path.

## Solution
Added a `variant_link_id` field to the `vendor_product_variants` table that references the specific link in the `variants_configurations_links` table.

## Changes Made

### 1. Database Migration
**File:** `Modules/CatalogManagement/database/migrations/2026_03_10_000000_add_variant_link_id_to_vendor_product_variants_table.php`

Added `variant_link_id` column to `vendor_product_variants` table:
- Type: Foreign key to `variants_configurations_links.id`
- Nullable: Yes (for simple products and backward compatibility)
- On Delete: SET NULL

### 2. Model Updates

#### VendorProductVariant Model
**File:** `Modules/CatalogManagement/app/Models/VendorProductVariant.php`

Added relationship:
```php
public function variantLink()
{
    return $this->belongsTo(VariantConfigurationLink::class, 'variant_link_id');
}
```

#### New VariantConfigurationLink Model
**File:** `Modules/CatalogManagement/app/Models/VariantConfigurationLink.php`

Created model for the `variants_configurations_links` table with relationships:
- `parentConfiguration()` - Parent variant
- `childConfiguration()` - Child variant
- `vendorProductVariants()` - All variants using this link

#### VariantsConfiguration Model
**File:** `Modules/CatalogManagement/app/Models/VariantsConfiguration.php`

Added helper methods:
- `getLinkIdToChild($childConfigId)` - Get link ID from this variant to a child
- `getLinkIdFromParent($parentConfigId)` - Get link ID from a parent to this variant
- `links()` - Get all links where this variant is the parent

### 3. Repository Updates
**File:** `Modules/CatalogManagement/app/Repositories/ProductRepository.php`

Updated `handleProductVariants()` method to:
- Accept `variant_link_id` in variant data during creation
- Accept `variant_link_id` in variant data during updates
- Store the link ID alongside the variant configuration ID

### 4. Controller & Routes
**File:** `Modules/CatalogManagement/app/Http/Controllers/VariantsConfigurationController.php`

Added new endpoint `getLinkId()` to retrieve the link ID between parent and child variants.

**File:** `Modules/CatalogManagement/routes/web.php`

Added route:
```php
Route::get('get-link-id', 'VariantsConfigurationController@getLinkId')
    ->name('variants-configurations.get-link-id');
```

**API Endpoint:**
```
GET /admin/variants-configurations/get-link-id?parent_id={parent_id}&child_id={child_id}
```

## Usage

### Frontend Implementation
When creating/updating a product with variants, include the `variant_link_id` in the variant data:

```javascript
{
    variant_configuration_id: 5,  // The child variant (e.g., "Small")
    variant_link_id: 10,           // The specific link (e.g., Red→Small)
    price: 200.00,
    sku: 'PROD-123',
    stocks: [...]
}
```

### How to Get variant_link_id

#### Method 1: Using the API Endpoint
When a user selects a variant path (e.g., Red → Small), call the API endpoint:

```javascript
// Example: User selected Red (parent_id: 3) → Small (child_id: 5)
fetch('/admin/variants-configurations/get-link-id?parent_id=3&child_id=5')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const variantLinkId = data.link_id;
            // Use this in your form submission
            variantData.variant_link_id = variantLinkId;
        }
    });
```

#### Method 2: Using Direct Database Query (Backend)
```php
$link = DB::table('variants_configurations_links')
    ->where('parent_config_id', 3)  // Red
    ->where('child_config_id', 5)   // Small
    ->first();

$variantLinkId = $link->id;  // Use this in the form
```

#### Method 3: Using the Model Helper Method
```php
$parentVariant = VariantsConfiguration::find(3); // Red
$linkId = $parentVariant->getLinkIdToChild(5);   // Get link to Small

// Or from child perspective
$childVariant = VariantsConfiguration::find(5);  // Small
$linkId = $childVariant->getLinkIdFromParent(3); // Get link from Red
```

### Backend Validation
The system now stores:
- `variant_configuration_id`: The final selected variant
- `variant_link_id`: The specific parent-child relationship used

This allows you to:
1. Know exactly which path was taken to reach the variant
2. Reconstruct the full variant hierarchy
3. Handle cases where the same variant is linked to multiple parents

## Migration Instructions

1. Run the migration:
```bash
php artisan migrate
```

2. Update frontend forms to include `variant_link_id` when submitting variant data

3. Existing data will have `variant_link_id = NULL` (backward compatible)

## Benefits

1. **Accurate Tracking**: Know exactly which parent-child relationship was used
2. **Data Integrity**: Prevent ambiguity when variants have multiple parents
3. **Better Reporting**: Generate accurate reports on variant usage by path
4. **Backward Compatible**: Nullable field doesn't break existing functionality
