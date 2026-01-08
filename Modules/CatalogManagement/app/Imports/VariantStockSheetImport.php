<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\ProductVariant;
use Modules\CatalogManagement\app\Models\VariantStock;

class VariantStockSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected array $variantsWithStock = [];

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

            if (!isset($this->variantMap[$excelVariantId])) {
                continue;
            }

            $dbVariantId = $this->variantMap[$excelVariantId];
            $variant = ProductVariant::find($dbVariantId);
            if (!$variant) {
                continue;
            }

            VariantStock::updateOrCreate(
                [
                    'product_variant_id' => $dbVariantId,
                    'region_id' => $regionId
                ],
                [
                    'stock' => $stock
                ]
            );

            $this->variantsWithStock[$excelVariantId] = true;
        }

        $this->recalculateStocks();
        $this->validateVariantsHaveStock();
    }

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

    private function recalculateStocks()
    {
        $variantIds = array_unique(array_values($this->variantMap));

        foreach ($variantIds as $variantId) {
            $variant = ProductVariant::find($variantId);
            if (!$variant) {
                continue;
            }

            $variantStock = VariantStock::where('product_variant_id', $variantId)->sum('stock');
            $variant->stock = $variantStock;
            $variant->save();

            $product = $variant->product;
            if ($product && !$product->trashed()) {
                $productStock = ProductVariant::where('product_id', $product->id)->sum('stock');
                $product->stock = $productStock;
                $product->save();
            }
        }
    }
}
