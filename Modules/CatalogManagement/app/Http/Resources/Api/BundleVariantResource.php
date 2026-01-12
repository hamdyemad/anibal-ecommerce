<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for vendor product variants within bundles.
 * Uses bundle-specific pricing instead of the variant's original price.
 */
class BundleVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        
        // Build configuration object with tree structure
        $configuration = null;
        if ($this->variantConfiguration) {
            $configuration = $this->buildConfigurationTree($this->variantConfiguration, $locale);
        }

        // Use bundle prices passed from BundleProductResource
        $priceBeforeTaxes = $this->bundle_price_before_taxes ?? (float) $this->price;
        $priceAfterTaxes = $this->bundle_price_after_taxes ?? $priceBeforeTaxes;
        $taxAmount = $this->bundle_tax_amount ?? 0;
        $taxPercentage = $this->bundle_tax_percentage ?? 0;
        
        $vendorProduct = $this->vendorProduct;
        
        return [
            'id' => $this->id,
            'show_end_offer_at_section' => false, // Bundles don't show offer countdown
            'stock' => $this->total_stock ?? 0,
            'booked_stock' => $this->booked_stock ?? 0,
            'allocated_stock' => $this->allocated_stock ?? 0,
            'fulfilled_stock' => $this->fulfilled_stock ?? 0,
            'remaining_stock' => $this->remaining_stock ?? 0,
            'sku' => $this->sku,
            'variant_name' => $this->{"variant_path_{$locale}"} ?? '',
            'variant_key' => $this->variantConfiguration && $this->variantConfiguration->key ? 
                ($this->variantConfiguration->key->getTranslation('name', $locale) ?? $this->variantConfiguration->key->name) : '',
            'variant_value' => $this->variantConfiguration ? 
                ($this->variantConfiguration->getTranslation('name', $locale) ?? ($this->variantConfiguration->name ?? $this->variantConfiguration->value)) : '',
            'configuration' => $configuration,
            'vendor_name' => $vendorProduct ? 
                ($vendorProduct->relationLoaded('vendor') && $vendorProduct->vendor ? $vendorProduct->vendor->name : null) : null,
            'price_before_taxes' => $this->formatPrice($priceBeforeTaxes),
            'price_after_taxes' => $this->formatPrice($priceAfterTaxes),
            'tax_amount' => $this->formatPrice($taxAmount),
            'tax_percentage' => $taxPercentage,
            'fake_price' => null, // Bundles don't have fake prices
            'discount' => null, // Bundles don't show variant discounts
            'quantity_in_cart' => $this->quantity_in_cart,
            'cart_id' => $this->cart_id,
        ];
    }

    /**
     * Build configuration tree recursively
     */
    private function buildConfigurationTree($configuration, $locale): array
    {
        $colorValue = null;
        if ($configuration->type === 'color') {
            $colorValue = $configuration->value;
        }
        
        $configData = [
            'id' => $configuration->id,
            'name' => $configuration->getTranslation('name', $locale) ?? $configuration->name ?? $configuration->value,
            'color' => $colorValue,
            'key' => $configuration->key ? [
                'id' => $configuration->key->id,
                'name' => $configuration->key->getTranslation('name', $locale) ?? $configuration->key->name,
            ] : null,
        ];
        
        if ($configuration->parent_id && $configuration->relationLoaded('parent_data') && $configuration->parent_data) {
            $configData['parent'] = $this->buildConfigurationTree($configuration->parent_data, $locale);
        }
        
        return $configData;
    }

    /**
     * Format price with thousand separator
     */
    private function formatPrice(float $price): string
    {
        return number_format($price, 2, '.', '');
    }
}
