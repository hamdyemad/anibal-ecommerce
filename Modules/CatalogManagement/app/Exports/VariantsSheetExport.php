<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;

/**
 * Sheet: variants
 * Exports product variants with pricing
 */
class VariantsSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithColumnFormatting
{
    protected bool $isAdmin;
    protected $vendorProducts;
    protected array $productIdMapping;

    public function __construct(bool $isAdmin = false, $vendorProducts = null, array $productIdMapping = [])
    {
        $this->isAdmin = $isAdmin;
        $this->vendorProducts = $vendorProducts;
        $this->productIdMapping = $productIdMapping;
    }

    public function collection()
    {
        // Extract all variants from vendor products
        $variants = new Collection();
        
        foreach ($this->vendorProducts as $vendorProduct) {
            foreach ($vendorProduct->variants as $variant) {
                // Store the parent product SKU for reference
                $variant->parent_product_sku = $vendorProduct->sku;
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
        // Determine if has discount
        $hasDiscount = !empty($variant->price_before_discount) && $variant->price_before_discount > $variant->price;
        
        // Format date as string to prevent Excel from reformatting it
        $discountEndDate = '';
        if ($variant->discount_end_date) {
            $discountEndDate = $variant->discount_end_date->format('Y-m-d');
        }
        
        return [
            $variant->parent_product_sku ?? '',
            $variant->sku ?? '',
            $variant->variant_configuration_id ?? '',
            $variant->price ?? 0,
            $hasDiscount ? 'yes' : 'no',
            $variant->price_before_discount ?? '',
            $discountEndDate,
        ];
    }

    /**
     * Format the discount_end_date column as text to preserve YYYY-MM-DD format
     */
    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }

    public function title(): string
    {
        return 'variants';
    }
}
