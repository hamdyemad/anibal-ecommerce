<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

/**
 * Sheet: variants
 * Exports vendor bank product variants with pricing
 */
class VendorBankVariantsSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $vendorProducts;
    protected array $productIdMapping;

    public function __construct($vendorProducts = null, array $productIdMapping = [])
    {
        $this->vendorProducts = $vendorProducts;
        $this->productIdMapping = $productIdMapping;
    }

    public function collection()
    {
        // Extract all variants from vendor products with product info
        $variants = new Collection();
        
        foreach ($this->vendorProducts as $vendorProduct) {
            foreach ($vendorProduct->variants as $variant) {
                // Attach product info to variant for easy access in map()
                $variant->_product_sku = $vendorProduct->sku;
                $variants->push($variant);
            }
        }
        
        return $variants;
    }

    public function headings(): array
    {
        return [
            'product_sku',
            'sku',
            'variant_configuration_id',
            'price',
            'has_discount',
            'price_before_discount',
            'discount_end_date',
        ];
    }

    public function map($variant): array
    {
        // Use the attached product SKU
        $productSku = $variant->_product_sku ?? '';
        
        // Determine if has discount
        $hasDiscount = !empty($variant->price_before_discount) && $variant->price_before_discount > $variant->price;
        
        return [
            $productSku,
            $variant->sku ?? '',
            $variant->variant_configuration_id ?? '',
            $variant->price ?? 0,
            $hasDiscount ? 'yes' : 'no',
            $variant->price_before_discount ?? '',
            $variant->offer_end_date ? $variant->offer_end_date->format('Y-m-d') : '',
        ];
    }

    public function title(): string
    {
        return 'variants';
    }
}
