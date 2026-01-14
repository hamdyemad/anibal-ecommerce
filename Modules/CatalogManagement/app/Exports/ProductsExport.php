<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Illuminate\Support\Facades\Auth;

/**
 * Products Export for Vendor Products
 * 
 * Structure matches import:
 * - products: Product data with SKU
 * - images: Product images
 * - variants: Product variants with pricing
 * - variant_stock: Stock per region
 */
class ProductsExport implements WithMultipleSheets
{
    protected bool $isAdmin;
    protected array $filters;
    protected bool $includeOccasions;
    protected array $productIdMapping = [];
    protected $vendorProducts;

    public function __construct(bool $isAdmin = false, array $filters = [], bool $includeOccasions = false)
    {
        $this->isAdmin = $isAdmin;
        $this->filters = $filters;
        $this->includeOccasions = $includeOccasions;
        $this->loadAllData();
    }

    /**
     * Load all data with relations in a single query
     * Build mapping from vendor_product_id to incremental index
     */
    protected function loadAllData()
    {
        $query = VendorProduct::with([
            'product.brand',
            'product.department',
            'product.category',
            'product.subCategory',
            'product.translations',
            'product.attachments' => function($q) {
                $q->whereIn('type', ['main_image', 'additional_image']);
            },
            'vendor',
            'variants.stocks.region',
            'variants.variantConfiguration.key'
        ]);

        // Apply vendor filter for non-admin users (vendors can only export their own products)
        if (!$this->isAdmin) {
            $vendorId = Auth::user()->vendor?->id;
            if (!$vendorId) {
                // If user is not admin and has no vendor, return empty collection
                $this->vendorProducts = collect();
                return;
            }
            $query->where('vendor_id', $vendorId);
        }

        // Apply all other filters using the scopeFilter
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
        $sheets = [
            new ProductsSheetExport($this->isAdmin, $this->vendorProducts, $this->productIdMapping),
            new ImagesSheetExport($this->isAdmin, $this->vendorProducts, $this->productIdMapping),
            new VariantsSheetExport($this->isAdmin, $this->vendorProducts, $this->productIdMapping),
            new VariantStockSheetExport($this->isAdmin, $this->vendorProducts, $this->productIdMapping),
        ];

        // Occasions sheets are optional (only if explicitly requested)
        if ($this->isAdmin && $this->includeOccasions) {
            $sheets[] = new OccasionsSheetExport($this->isAdmin, $this->filters);
            $sheets[] = new OccasionProductsSheetExport($this->isAdmin, $this->filters);
        }
        
        return $sheets;
    }
}
