<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Vendor Bank Products Demo Export
 * 
 * Provides a demo Excel file with 2 sheets for vendors to understand the structure
 */
class VendorBankProductsDemoExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new VendorBankVariantsDemoSheetExport(),
            new VendorBankVariantStockDemoSheetExport(),
        ];
    }
}
