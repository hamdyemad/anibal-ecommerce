<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

/**
 * Sheet: variant_stock
 * Exports vendor bank product variant stock per region
 */
class VendorBankVariantStockSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
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
        // Extract all variant stocks from vendor products
        $stocks = new Collection();
        
        foreach ($this->vendorProducts as $vendorProduct) {
            foreach ($vendorProduct->variants as $variant) {
                foreach ($variant->stocks as $stock) {
                    $stocks->push($stock);
                }
            }
        }
        
        return $stocks;
    }

    public function headings(): array
    {
        return [
            'variant_sku',
            'region_id',
            'stock',
        ];
    }

    public function map($stock): array
    {
        return [
            $stock->vendorProductVariant->sku ?? '',
            $stock->region_id,
            $stock->quantity ?? 0,
        ];
    }

    public function title(): string
    {
        return 'variant_stock';
    }
}
