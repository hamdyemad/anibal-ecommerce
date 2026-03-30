<?php

namespace Modules\Order\app\Traits;

trait HasVariantConfigurationTree
{
    /**
     * Build configuration tree for the selected variant only
     * Returns nested structure: key -> value -> key -> value -> ... -> variant
     */
    protected function buildVariantConfigurationTree($configuration, $variantId, $locale): array
    {
        if (!$configuration || !$configuration->key) {
            return [];
        }

        // Build the hierarchy from bottom to top
        $hierarchy = [];
        $currentConfig = $configuration;
        
        // Collect all configurations from child to parent
        while ($currentConfig) {
            $hierarchy[] = $currentConfig;
            
            // Move to parent if exists
            if ($currentConfig->parent_id && $currentConfig->relationLoaded('parent_data')) {
                $currentConfig = $currentConfig->parent_data;
            } else {
                break;
            }
        }
        
        // Reverse to get parent -> child order
        $hierarchy = array_reverse($hierarchy);
        
        // Build nested structure
        return $this->buildNestedStructure($hierarchy, $variantId, $locale);
    }
    
    /**
     * Build nested structure recursively
     */
    private function buildNestedStructure(array $hierarchy, $variantId, $locale, $index = 0): array
    {
        if ($index >= count($hierarchy)) {
            return [];
        }
        
        $config = $hierarchy[$index];
        $key = $config->key;
        
        // Get color value - only if type is 'color', use the value field
        $colorValue = null;
        if ($config->type === 'color' && $config->value) {
            $colorValue = $config->value;
        }
        
        // Build value node
        $valueNode = [
            'id' => $config->id,
            'name' => $config->getTranslation('name', $locale) ?? $config->name ?? $config->value,
            'value' => $config->value,
            'type' => $config->type,
            'color' => $colorValue,
            'key_id' => $config->key_id,
        ];
        
        // If this is the last level (leaf), add variant info
        if ($index === count($hierarchy) - 1) {
            // This is the selected variant - no children, just mark as selected
            $valueNode['is_selected'] = true;
            $valueNode['variant_id'] = $variantId;
            $valueNode['parent_id'] = $config->parent_id;
        } else {
            // Not the last level, add children recursively
            $valueNode['children'] = $this->buildNestedStructure($hierarchy, $variantId, $locale, $index + 1);
        }
        
        // Build key node
        $keyNode = [
            'id' => $key->id,
            'name' => $key->getTranslation('name', $locale) ?? $key->name,
            'type' => 'key',
            'children' => [$valueNode]
        ];
        
        return [$keyNode];
    }
}
