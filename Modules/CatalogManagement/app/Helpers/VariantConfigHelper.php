<?php

namespace Modules\CatalogManagement\app\Helpers;

class VariantConfigHelper
{
    /**
     * Build variant configuration tree HTML by traversing parent relationships
     * 
     * @param mixed $variant The variant object with variantConfiguration relationship
     * @param string $locale The locale for translations
     * @return string HTML string with configuration badges
     */
    public static function buildConfigTreeHtml($variant, string $locale): string
    {
        if (!$variant || !$variant->variantConfiguration) {
            return '';
        }
        
        $configTree = $variant->variantConfiguration;
        $configs = [];
        
        // Traverse up the parent tree to get all configurations
        while ($configTree) {
            $configs[] = [
                'key' => $configTree->variantConfigurationKey?->getTranslation('name', $locale),
                'value' => $configTree->getTranslation('value', $locale)
            ];
            $configTree = $configTree->parent;
        }
        $configs = array_reverse($configs);
        
        $html = '<div class="mt-2">';
        foreach ($configs as $config) {
            $html .= '<span class="badge bg-light text-dark border me-1 mb-1">';
            $html .= '<strong>' . htmlspecialchars($config['key']) . ':</strong> ';
            $html .= htmlspecialchars($config['value']);
            $html .= '</span>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Get variant configuration as array
     * 
     * @param mixed $variant The variant object with variantConfiguration relationship
     * @param string $locale The locale for translations
     * @return array Array of configuration items with key and value
     */
    public static function getConfigTreeArray($variant, string $locale): array
    {
        if (!$variant || !$variant->variantConfiguration) {
            return [];
        }
        
        $configTree = $variant->variantConfiguration;
        $configs = [];
        
        // Traverse up the parent tree to get all configurations
        while ($configTree) {
            $configs[] = [
                'key' => $configTree->variantConfigurationKey?->getTranslation('name', $locale),
                'value' => $configTree->getTranslation('value', $locale)
            ];
            $configTree = $configTree->parent;
        }
        
        return array_reverse($configs);
    }
}
