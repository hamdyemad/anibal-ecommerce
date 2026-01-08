<?php

namespace Modules\CatalogManagement\app\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * FINAL Products Import (DEMO + EXPORT compatible)
 *
 * Important:
 * - SoftDeletes exists in products table only
 * - Other tables (variants / stock_regions / occasions / pivot ...) without SoftDeletes
 *
 * Guarantees:
 * - products: create if not exists (active), SKIP if sku exists as soft-deleted
 * - variants: create/update price only
 * - variant_stock: upsert stocks then recalc variant/product stock
 * - occasions + pivot: upsert only (no soft delete checks) - ADMIN ONLY
 */
class ProductsImport implements WithMultipleSheets
{
    public array $productMap  = []; // excel_product_id -> db_product_id
    public array $variantMap  = []; // excel_variant_key -> db_variant_id
    public array $occasionMap = []; // excel_occasion_id -> db_occasion_id
    public array $productSkuToDbId = []; // sku -> db_product_id
    public array $variantSkuToDbId = []; // "{productId}|{sku}" -> db_variant_id
    public array $errors = []; // Import errors
    public array $productsWithVariants = []; // Track products that have_varient = yes
    
    protected ProductsSheetImport $productsSheet;
    protected VariantsSheetImport $variantsSheet;
    protected VariantStockSheetImport $variantStockSheet;
    protected bool $isAdmin;

    public function __construct(bool $isAdmin = false)
    {
        $this->isAdmin = $isAdmin;
    }

    public function sheets(): array
    {
        $this->productsSheet = new ProductsSheetImport($this->productMap, $this->productSkuToDbId, $this->errors, $this->productsWithVariants);
        $this->variantsSheet = new VariantsSheetImport($this->productMap, $this->variantMap, $this->variantSkuToDbId, $this->errors);
        $this->variantStockSheet = new VariantStockSheetImport($this->variantMap, $this->errors);
        
        $sheets = [
            'products'          => $this->productsSheet,
            'images'            => new ImagesSheetImport($this->productMap, $this->errors),
            'variants'          => $this->variantsSheet,
            'variant_stock'     => $this->variantStockSheet,
        ];

        // Occasions sheets are only for admin imports (vendor products with occasions)
        if ($this->isAdmin) {
            $sheets['occasions'] = new OccasionsSheetImport($this->occasionMap, $this->errors);
            $sheets['occasion_products'] = new OccasionProductsSheetImport($this->occasionMap, $this->productMap, $this->variantMap, $this->errors);
        }
        
        return $sheets;
    }

    /**
     * Get count of imported products
     */
    public function getImportedCount(): int
    {
        return count($this->productMap);
    }

    /**
     * Get import errors from all sheets
     */
    public function getErrors(): array
    {
        // After all sheets are processed, validate products with variants
        $this->validateProductsWithVariants();
        
        return $this->errors;
    }

    /**
     * Validate that products with have_varient = yes have variants and variant stock
     */
    protected function validateProductsWithVariants(): void
    {
        foreach ($this->productsWithVariants as $excelProductId => $productInfo) {
            // Check if product was successfully imported
            if (!isset($this->productMap[$excelProductId])) {
                continue;
            }

            $dbProductId = $this->productMap[$excelProductId];

            // Check if product has variants in the variants sheet
            $productVariants = [];
            foreach ($this->variantMap as $excelVariantId => $dbVariantId) {
                // Check if this variant belongs to this product by checking the variantSkuToDbId map
                foreach ($this->variantSkuToDbId as $key => $varId) {
                    if ($varId == $dbVariantId && str_starts_with($key, $dbProductId . '|')) {
                        $productVariants[] = $excelVariantId;
                        break;
                    }
                }
            }

            if (empty($productVariants)) {
                $this->errors[] = [
                    'sheet' => 'products',
                    'row' => $productInfo['row'],
                    'sku' => $productInfo['sku'],
                    'errors' => ['Product has have_varient = yes but no variants found in variants sheet. Please add variants for this product.']
                ];
            }
        }
    }
}
