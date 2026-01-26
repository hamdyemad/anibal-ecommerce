<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\Product;

/**
 * Vendor Bank Products Export
 * 
 * For vendors to export their variants and stocks for bank products
 * Only includes 2 sheets:
 * - variants: Product variants with pricing
 * - variant_stock: Stock per region
 */
class VendorBankProductsExport implements WithMultipleSheets
{
    protected int $vendorId;
    protected array $filters;
    protected array $productIdMapping = [];
    protected $vendorProducts;

    public function __construct(int $vendorId, array $filters = [])
    {
        $this->vendorId = $vendorId;
        $this->filters = $filters;
        $this->loadAllData();
    }

    /**
     * Load all vendor's bank products with relations
     */
    protected function loadAllData()
    {
        $query = VendorProduct::with([
            'product.brand',
            'product.department',
            'product.category',
            'product.translations',
            'vendor',
            'variants.stocks.region',
            'variants.variantConfiguration.key'
        ])
        ->where('vendor_id', $this->vendorId)
        ->whereHas('product', function($q) {
            $q->where('type', Product::TYPE_BANK);
        });

        // Use scope for filtering
        $query->filter($this->filters);

        // Load all data
        $this->vendorProducts = $query->orderBy('id')->get();

        // Build product ID mapping
        $index = 1;
        foreach ($this->vendorProducts as $vendorProduct) {
            $this->productIdMapping[$vendorProduct->id] = $index++;
        }
    }

    public function sheets(): array
    {
        return [
            new VendorBankVariantsSheetExport($this->vendorProducts, $this->productIdMapping),
            new VendorBankVariantStockSheetExport($this->vendorProducts, $this->productIdMapping),
        ];
    }
}
