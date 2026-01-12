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
        $bundlePriceBeforeTaxes = $this->bundle_price_before_taxes ?? (float) $this->price;
        $bundlePriceAfterTaxes = $this->bundle_price_after_taxes ?? $bundlePriceBeforeTaxes;
        $bundleTaxAmount = $this->bundle_tax_amount ?? 0;
        $bundleTaxPercentage = $this->bundle_tax_percentage ?? 0;
        
        $vendorProduct = $this->vendorProduct;
        
        // Calculate original variant prices (before bundle pricing)
        $originalPriceBeforeTaxes = (float) $this->price;
        $originalPriceAfterTaxes = $originalPriceBeforeTaxes;
        $originalTaxAmount = 0;
        $originalTaxPercentage = 0;
        $originalTaxes = [];
        
        // Get taxes from vendor product
        if ($vendorProduct && $vendorProduct->relationLoaded('taxes') && $vendorProduct->taxes->count() > 0) {
            $originalTaxPercentage = $vendorProduct->taxes->sum('percentage');
            $taxMultiplier = 1 + ($originalTaxPercentage / 100);
            $originalPriceAfterTaxes = $originalPriceBeforeTaxes * $taxMultiplier;
            $originalTaxAmount = $originalPriceAfterTaxes - $originalPriceBeforeTaxes;
            
            // Build taxes array
            $originalTaxes = $vendorProduct->taxes->map(function ($tax) use ($originalPriceBeforeTaxes) {
                $taxValue = $originalPriceBeforeTaxes * ($tax->percentage / 100);
                return [
                    'id' => $tax->id,
                    'name' => $tax->name,
                    'percentage' => $tax->percentage,
                    'amount' => round($taxValue, 2),
                ];
            })->toArray();
        }
        
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
            // Bundle prices (what customer pays in bundle)
            'price_before_taxes' => $this->formatPrice($bundlePriceBeforeTaxes),
            'price_after_taxes' => $this->formatPrice($bundlePriceAfterTaxes),
            'tax_amount' => $this->formatPrice($bundleTaxAmount),
            'tax_percentage' => $bundleTaxPercentage,
            // Original variant prices (actual variant price without bundle discount)
            'original_price_before_taxes' => $this->formatPrice($originalPriceBeforeTaxes),
            'original_price_after_taxes' => $this->formatPrice($originalPriceAfterTaxes),
            'original_tax_amount' => $this->formatPrice($originalTaxAmount),
            'original_tax_percentage' => $originalTaxPercentage,
            'original_taxes' => $originalTaxes,
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
