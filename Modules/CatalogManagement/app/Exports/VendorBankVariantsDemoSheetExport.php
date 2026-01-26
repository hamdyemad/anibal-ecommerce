<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Demo Sheet: variants
 * Provides example data for vendor bank product variants
 */
class VendorBankVariantsDemoSheetExport implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [
            [
                'PROD-001',
                'VAR-001',
                123,
                100.00,
                'no',
                '',
                '',
            ],
            [
                'PROD-001',
                'VAR-002',
                124,
                95.00,
                'yes',
                120.00,
                '2024-12-31',
            ],
        ];
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

    public function title(): string
    {
        return 'variants';
    }
}
