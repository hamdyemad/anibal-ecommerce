<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\ProductVariant;
use Modules\CatalogManagement\app\Models\VariantStock;

/**
 * Sheet: variant_stock
 * - variant_id from excel -> lookup in variantMap
 * - upsert stock by variant_id + region_id (no soft delete checks)
 * - recalculate variant and product stock after all imports
 */
class VariantStockSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected array $variantsWithStock = []; // Track which variants have stock entries

    public function __construct(
        protected array &$variantMap,
        protected array &$importErrors = []
    ) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelVariantId = (int)($row['variant_id'] ?? 0);
            $regionId = (int)($row['region_id'] ?? 0);
            $stock = (int)($row['stock'] ?? 0);

            // Validate row data
            $validator = Validator::make($row->toArray(), [
                'variant_id' => 'required|integer|min:1',
                'region_id' => 'required|integer|exists:regions,id',
                'stock' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'variant_stock',
                    'row' => $index + 2,
                    'variant_id' => $excelVariantId,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($excelVariantId <= 0 || $regionId <= 0) {
                $this->importErrors[] = [
                    'sheet' => 'variant_stock',
                    'row' => $index + 2,
                    'variant_id' => $excelVariantId,
                    'errors' => ['Invalid variant ID or region ID']
                ];
                continue;
            }

            // Skip if variant not in map
            if (!isset($this->variantMap[$excelVariantId])) {
                continue;
            }

            $dbVariantId = $this->variantMap[$excelVariantId];

            // Check if variant exists
            $variant = ProductVariant::find($dbVariantId);
            if (!$variant) {
                continue;
            }

            // Upsert stock
            VariantStock::updateOrCreate(
                [
                    'product_variant_id' => $dbVariantId,
                    'region_id' => $regionId
                ],
                [
                    'stock' => $stock
                ]
            );

            // Track that this variant has stock
            $this->variantsWithStock[$excelVariantId] = true;
        }

        // After all stock imports, recalculate variant and product stocks
        $this->recalculateStocks();
        
        // Validate that all variants have stock entries
        $this->validateVariantsHaveStock();
    }

    /**
     * Validate that all imported variants have stock entries
     */
    protected function validateVariantsHaveStock(): void
    {
        foreach ($this->variantMap as $excelVariantId => $dbVariantId) {
            if (!isset($this->variantsWithStock[$excelVariantId])) {
                $this->importErrors[] = [
                    'sheet' => 'variant_stock',
                    'row' => '-',
                    'variant_id' => $excelVariantId,
                    'errors' => ['Variant exists but has no stock entries in variant_stock sheet']
                ];
            }
        }
    }

    /**
     * Recalculate variant stock (sum of all regions) and product stock (sum of all variants)
     */
    private function recalculateStocks()
    {
        // Get all unique variant IDs from the map
        $variantIds = array_unique(array_values($this->variantMap));

        foreach ($variantIds as $variantId) {
            $variant = ProductVariant::find($variantId);
            if (!$variant) {
                continue;
            }

            // Calculate variant stock (sum of all regions)
            $variantStock = VariantStock::where('product_variant_id', $variantId)->sum('stock');
            $variant->stock = $variantStock;
            $variant->save();

            // Calculate product stock (sum of all variants)
            $product = $variant->product;
            if ($product && !$product->trashed()) {
                $productStock = ProductVariant::where('product_id', $product->id)->sum('stock');
                $product->stock = $productStock;
                $product->save();
            }
        }
    }
}
