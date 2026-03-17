# VariantTreeHelper Usage Guide

The `VariantTreeHelper` is a reusable helper class for building variant configuration trees across different parts of the application.

## Available Methods

### 1. `buildConfigurationTree(Collection $variants, $taxes = [], string $locale = null): array`

Build a complete configuration tree from a collection of variants.

**Usage in Resources:**
```php
// In VendorProductResource
private function buildConfigurationTree(): array
{
    $variants = $this->variants;
    $taxes = $this->taxes ?? collect();
    $locale = app()->getLocale();
    
    return \App\Helpers\VariantTreeHelper::buildConfigurationTree($variants, $taxes, $locale);
}
```

### 2. `buildSimpleProductTree($variant, $product, $taxes = [], string $locale = null): array`

Build tree for simple (non-variant) products.

**Usage:**
```php
$tree = \App\Helpers\VariantTreeHelper::buildSimpleProductTree(
    $variant, 
    $product, 
    $taxes, 
    app()->getLocale()
);
```

### 3. `buildVariantHierarchyString($variant, string $locale = null, string $separator = ' → '): string`

Build a display string showing the full hierarchy path.

**Usage in Blade Templates:**
```blade
@php
$hierarchyString = \App\Helpers\VariantTreeHelper::buildVariantHierarchyString($variant, app()->getLocale());
@endphp
<span>{{ $hierarchyString }}</span>
```

**Usage in Controllers:**
```php
$hierarchyString = \App\Helpers\VariantTreeHelper::buildVariantHierarchyString($variant);
// Output: "Color → Red → Size → Medium"
```

### 4. `buildSingleVariantTree($variant, $taxes = [], string $locale = null): array`

Build tree structure for a single variant (useful for cart items, order items).

**Usage in Order/Cart Resources:**
```php
// In OrderProductResource or CartResource
'configuration_tree' => \App\Helpers\VariantTreeHelper::buildSingleVariantTree(
    $this->vendorProductVariant,
    $taxes,
    app()->getLocale()
)
```

### 5. `calculateVariantPrices($variant, float $taxMultiplier = 1): array`

Calculate variant prices with taxes applied.

**Usage:**
```php
$taxMultiplier = 1.15; // 15% tax
$priceData = \App\Helpers\VariantTreeHelper::calculateVariantPrices($variant, $taxMultiplier);
// Returns: ['id', 'sku', 'stock', 'price_before_taxes', 'real_price', 'fake_price', etc.]
```

## Integration Examples

### 1. Order Resources
```php
// In OrderProductResource.php
'configuration_tree' => $this->when(
    $this->vendorProductVariant && 
    $this->vendorProductVariant->relationLoaded('variantConfiguration'),
    function() {
        return \App\Helpers\VariantTreeHelper::buildSingleVariantTree(
            $this->vendorProductVariant,
            $this->vendorProduct->taxes ?? [],
            app()->getLocale()
        );
    }
)
```

### 2. Cart Resources
```php
// In CartProductResource.php
'configuration_tree' => $this->when(
    $this->relationLoaded('variantConfiguration') && $this->variantConfiguration,
    function() {
        return \App\Helpers\VariantTreeHelper::buildSingleVariantTree(
            $this,
            $this->vendorProduct->taxes ?? [],
            app()->getLocale()
        );
    }
)
```

### 3. Bundle/Occasion Resources
```php
// In BundleProductResource.php or OccasionProductResource.php
'configuration_tree' => \App\Helpers\VariantTreeHelper::buildSingleVariantTree(
    $this->vendorProductVariant,
    [], // No taxes for bundle/occasion pricing
    app()->getLocale()
)
```

### 4. Admin Panel Display
```blade
{{-- In admin product lists --}}
@foreach($product->variants as $variant)
    <div class="variant-item">
        <strong>{{ \App\Helpers\VariantTreeHelper::buildVariantHierarchyString($variant) }}</strong>
        <span class="price">${{ $variant->price }}</span>
    </div>
@endforeach
```

### 5. API Responses
```php
// In any API controller
public function getProductVariants($productId)
{
    $variants = VendorProductVariant::with([
        'variantConfiguration',
        'variantLink.parentConfiguration.key',
        'variantLink.childConfiguration.key'
    ])->where('vendor_product_id', $productId)->get();
    
    return response()->json([
        'variants' => $variants->map(function($variant) {
            return [
                'id' => $variant->id,
                'hierarchy' => \App\Helpers\VariantTreeHelper::buildVariantHierarchyString($variant),
                'price_data' => \App\Helpers\VariantTreeHelper::calculateVariantPrices($variant, 1.15),
                'tree' => \App\Helpers\VariantTreeHelper::buildSingleVariantTree($variant)
            ];
        })
    ]);
}
```

## Required Relationships

For the helper to work properly, make sure these relationships are loaded:

```php
// Minimum required relationships
$variants = VendorProductVariant::with([
    'variantConfiguration.key',
    'variantLink.parentConfiguration.key',
    'variantLink.childConfiguration.key'
])->get();

// Full relationships for complete functionality
$variants = VendorProductVariant::with([
    'variantConfiguration.key.translations',
    'variantConfiguration.parent_data.key.translations',
    'variantLink.parentConfiguration.key.translations',
    'variantLink.childConfiguration.key.translations'
])->get();
```

## Benefits

1. **Consistency**: Same tree structure across all parts of the application
2. **Reusability**: Use in resources, controllers, blade templates, APIs
3. **Maintainability**: Single place to update tree building logic
4. **Performance**: Optimized to use loaded relationships efficiently
5. **Flexibility**: Support for both variant_link_id and fallback to parent_data chain
6. **Localization**: Built-in support for multiple languages

## Migration Path

To migrate existing code to use the helper:

1. **Replace inline tree building** with `buildConfigurationTree()`
2. **Replace hierarchy display logic** with `buildVariantHierarchyString()`
3. **Update resources** to use helper methods
4. **Ensure proper eager loading** of required relationships