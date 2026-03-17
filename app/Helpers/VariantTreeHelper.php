<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;

class VariantTreeHelper
{
    /**
     * Build configuration tree from variants collection
     * 
     * @param Collection $variants Collection of VendorProductVariant models
     * @param array $taxes Tax collection for price calculation
     * @param string|null $locale Language locale
     * @return array
     */
    public static function buildConfigurationTree(Collection $variants, $taxes = [], ?string $locale = null): array
    {
        if ($variants->isEmpty()) {
            return [];
        }

        $locale = $locale ?? app()->getLocale();
        
        // Calculate tax multiplier
        $totalTaxPercentage = collect($taxes)->sum('percentage');
        $taxMultiplier = 1 + ($totalTaxPercentage / 100);

        $tree = [];
        
        foreach ($variants as $variant) {
            if (!$variant->variant_configuration_id) {
                continue;
            }

            // Calculate prices with taxes
            $variantData = self::calculateVariantPrices($variant, $taxMultiplier);

            // Build hierarchy path for this variant
            $hierarchyPath = self::buildVariantHierarchyPath($variant, $locale);
            
            // Add this variant to the tree using its hierarchy path
            self::addVariantToTree($tree, $hierarchyPath, $variantData);
        }

        return array_values($tree);
    }

    /**
     * Build the full hierarchy path for a variant using variant_link_id or fallback to parent_data
     * 
     * @param mixed $variant Variant model with relationships loaded
     * @param string $locale Language locale
     * @return array
     */
    public static function buildVariantHierarchyPath($variant, string $locale): array
    {
        $path = [];
        
        // If variant has a link with path, use it to get the full hierarchy
        if ($variant->variantLink && 
            $variant->variantLink->path && 
            is_array($variant->variantLink->path)) {
            
            // Use the stored path from the link
            $pathIds = $variant->variantLink->path;
            
            // Load all configurations in the path
            $configurations = VariantsConfiguration::whereIn('id', $pathIds)
                ->with(['key.translations', 'translations'])
                ->get()
                ->keyBy('id');
            
            // Build path items in the correct order
            foreach ($pathIds as $configId) {
                $config = $configurations->get($configId);
                if ($config && $config->key) {
                    $path[] = self::buildPathItem($config, $locale);
                }
            }
            
            return $path;
        }
        
        // Fallback: use the variant's configuration and traverse parent_data chain
        $current = $variant->variantConfiguration;
        $visited = [];
        
        while ($current && !in_array($current->id, $visited)) {
            $visited[] = $current->id;
            
            if ($current->key) {
                array_unshift($path, self::buildPathItem($current, $locale));
            }
            
            $current = $current->parent_data ?? null;
        }
        
        return $path;
    }

    /**
     * Build a clean path item from a variant configuration
     * 
     * @param mixed $config Variant configuration model
     * @param string $locale Language locale
     * @return array
     */
    private static function buildPathItem($config, string $locale): array
    {
        return [
            'key_id' => $config->key->id,
            'key_name' => $config->key->getTranslation('name', $locale) ?? $config->key->name,
            'config_id' => $config->id,
            'config_name' => $config->getTranslation('name', $locale) ?? $config->name,
            'config_value' => $config->value,
            'config_type' => $config->type,
            'color' => $config->type === 'color' ? $config->value : null,
        ];
    }

    /**
     * Add a variant to the tree structure using its hierarchy path
     * 
     * @param array $tree Tree array (passed by reference)
     * @param array $hierarchyPath Hierarchy path array
     * @param array $variantData Variant data array
     * @return void
     */
    public static function addVariantToTree(array &$tree, array $hierarchyPath, array $variantData): void
    {
        if (empty($hierarchyPath)) {
            return;
        }

        $currentLevel = &$tree;
        
        foreach ($hierarchyPath as $index => $pathItem) {
            $keyId = $pathItem['key_id'];
            $isLastLevel = ($index === count($hierarchyPath) - 1);
            
            // Find or create the key group
            $keyIndex = null;
            foreach ($currentLevel as $i => $item) {
                if ($item['id'] == $keyId && $item['type'] == 'key') {
                    $keyIndex = $i;
                    break;
                }
            }
            
            if ($keyIndex === null) {
                // Create new key group
                $currentLevel[] = [
                    'id' => $keyId,
                    'name' => $pathItem['key_name'],
                    'type' => 'key',
                    'children' => [],
                ];
                $keyIndex = count($currentLevel) - 1;
            }
            
            // Add the configuration as a child
            $configId = $pathItem['config_id'];
            $childExists = false;
            
            foreach ($currentLevel[$keyIndex]['children'] as &$child) {
                if ($child['id'] === $configId) {
                    $childExists = true;
                    if ($isLastLevel) {
                        // This is the final level, add the variant data
                        $child['variant'] = $variantData;
                    }
                    break;
                }
            }
            
            if (!$childExists) {
                $newChild = [
                    'id' => $configId,
                    'name' => $pathItem['config_name'],
                    'value' => $pathItem['config_value'],
                    'type' => $pathItem['config_type'],
                    'color' => $pathItem['color'],
                    'key_id' => $keyId,
                ];
                
                if ($isLastLevel) {
                    $newChild['variant'] = $variantData;
                } else {
                    $newChild['children'] = [];
                }
                
                $currentLevel[$keyIndex]['children'][] = $newChild;
            }
            
            // Move to the next level if not the last
            if (!$isLastLevel) {
                // Find the child we just created/updated and move to its children
                foreach ($currentLevel[$keyIndex]['children'] as &$child) {
                    if ($child['id'] === $configId) {
                        if (!isset($child['children'])) {
                            $child['children'] = [];
                        }
                        $currentLevel = &$child['children'];
                        break;
                    }
                }
            }
        }
    }

    /**
     * Build configuration tree for simple products
     * 
     * @param mixed $variant Single variant model
     * @param mixed $product Product model
     * @param array $taxes Tax collection
     * @param string|null $locale Language locale
     * @return array
     */
    public static function buildSimpleProductTree($variant, $product, $taxes = [], ?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        
        // Calculate tax multiplier
        $totalTaxPercentage = collect($taxes)->sum('percentage');
        $taxMultiplier = 1 + ($totalTaxPercentage / 100);

        // Calculate prices with taxes
        $variantData = self::calculateVariantPrices($variant, $taxMultiplier);

        return [
            [
                'id' => 0,
                'name' => $product->title ?? __('catalogmanagement::product.simple'),
                'type' => 'simple',
                'children' => [
                    [
                        'id' => 0,
                        'name' => $product->title ?? __('catalogmanagement::product.simple'),
                        'value' => null,
                        'type' => 'simple',
                        'key_id' => 0,
                        'parent_id' => null,
                        'variant' => $variantData
                    ]
                ]
            ]
        ];
    }

    /**
     * Calculate variant prices with taxes
     * 
     * @param mixed $variant Variant model
     * @param float $taxMultiplier Tax multiplier (1 + tax_percentage/100)
     * @return array
     */
    public static function calculateVariantPrices($variant, float $taxMultiplier = 1): array
    {
        $priceBeforeTax = (float) ($variant->price ?? 0);
        $priceAfterTax = $priceBeforeTax * $taxMultiplier;
        $fakePriceBeforeTax = $variant->price_before_discount ? (float) $variant->price_before_discount : null;
        $fakePriceAfterTax = $fakePriceBeforeTax ? $fakePriceBeforeTax * $taxMultiplier : null;

        return [
            'id' => $variant->id,
            'sku' => $variant->sku,
            'stock' => $variant->total_stock ?? 0,
            'remaining_stock' => $variant->remaining_stock ?? 0,
            'price_before_taxes' => number_format($priceBeforeTax, 2, '.', ''),
            'real_price' => number_format($priceAfterTax, 2, '.', ''),
            'fake_price' => $fakePriceAfterTax ? number_format($fakePriceAfterTax, 2, '.', '') : null,
            'discount' => $variant->discount ?? null,
            'quantity_in_cart' => $variant->quantity_in_cart ?? 0,
            'cart_id' => $variant->cart_id ?? null,
        ];
    }

    /**
     * Build variant hierarchy display string (for UI display)
     * 
     * @param mixed $variant Variant model with relationships loaded
     * @param string|null $locale Language locale
     * @param string $separator Separator between hierarchy levels
     * @return string
     */
    public static function buildVariantHierarchyString($variant, ?string $locale = null, string $separator = ' → '): string
    {
        $locale = $locale ?? app()->getLocale();
        $hierarchyPath = self::buildVariantHierarchyPath($variant, $locale);
        
        $pathStrings = [];
        foreach ($hierarchyPath as $pathItem) {
            $pathStrings[] = $pathItem['key_name'] . ' → ' . $pathItem['config_name'];
        }
        
        return implode($separator, $pathStrings);
    }

    /**
     * Get variant configuration tree for a single variant (useful for order items, cart items, etc.)
     * 
     * @param mixed $variant Variant model
     * @param array $taxes Tax collection
     * @param string|null $locale Language locale
     * @return array
     */
    public static function buildSingleVariantTree($variant, $taxes = [], ?string $locale = null): array
    {
        if (!$variant || !$variant->variant_configuration_id) {
            return [];
        }

        $locale = $locale ?? app()->getLocale();
        
        // Calculate tax multiplier
        $totalTaxPercentage = collect($taxes)->sum('percentage');
        $taxMultiplier = 1 + ($totalTaxPercentage / 100);

        // Calculate prices with taxes
        $variantData = self::calculateVariantPrices($variant, $taxMultiplier);

        // Build hierarchy path
        $hierarchyPath = self::buildVariantHierarchyPath($variant, $locale);
        
        // Build tree structure
        $tree = [];
        self::addVariantToTree($tree, $hierarchyPath, $variantData);
        
        return array_values($tree);
    }
}