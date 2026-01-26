<?php

namespace Modules\CatalogManagement\app\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

/**
 * Vendor Bank Products Import
 * 
 * Handles import of vendor bank product variants and stocks
 * Only 2 sheets: variants and variant_stock
 */
class VendorBankProductsImport implements WithMultipleSheets
{
    protected int $vendorId;
    protected int $userId;
    protected array $errors = [];

    public function __construct(int $vendorId, int $userId)
    {
        $this->vendorId = $vendorId;
        $this->userId = $userId;
    }

    public function sheets(): array
    {
        return [
            'variants' => new VendorBankVariantsSheetImport($this->vendorId, $this->userId, $this),
            'variant_stock' => new VendorBankVariantStockSheetImport($this->vendorId, $this->userId, $this),
        ];
    }

    public function addError(string $sheet, int $row, string $sku, $errors)
    {
        $this->errors[] = [
            'sheet' => $sheet,
            'row' => $row,
            'sku' => $sku,
            'errors' => is_array($errors) ? $errors : [$errors]
        ];
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get count of imported variants
     */
    public function getImportedCount(): int
    {
        // This would need to be tracked in the sheet imports
        // For now, return 0 as a placeholder
        // TODO: Implement proper counting in VendorBankVariantsSheetImport
        return 0;
    }
}
