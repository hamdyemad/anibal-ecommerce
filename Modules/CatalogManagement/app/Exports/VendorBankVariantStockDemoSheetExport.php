<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Demo Sheet: variant_stock
 * Provides example data for vendor bank product variant stocks
 */
class VendorBankVariantStockDemoSheetExport implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [
            [
                'VAR-001',
                1,
                50,
            ],
            [
                'VAR-001',
                2,
                30,
            ],
            [
                'VAR-002',
                1,
                40,
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'variant_sku',
            'region_id',
            'stock',
        ];
    }

    public function title(): string
    {
        return 'variant_stock';
    }
}
