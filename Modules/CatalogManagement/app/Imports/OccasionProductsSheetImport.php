<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\ProductVariant;
use Modules\CatalogManagement\app\Models\OccasionProduct;

class OccasionProductsSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function __construct(
        protected array &$occasionMap,
        protected array &$productMap,
        protected array &$variantMap,
        protected array &$importErrors = []
    ) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelOccasionId = (int)($row['occasion_id'] ?? 0);
            $excelProductId = (int)($row['product_id'] ?? 0);
            $excelVariantId = isset($row['variant_id']) ? (int)$row['variant_id'] : null;

            $validator = Validator::make($row->toArray(), [
                'occasion_id' => 'required|integer|min:1',
                'product_id' => 'required|integer|min:1',
                'variant_id' => 'required|integer|min:1',
                'special_price' => 'nullable|numeric|min:0',
                'position' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'occasion_products',
                    'row' => $index + 2,
                    'occasion_id' => $excelOccasionId,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($excelOccasionId <= 0 || $excelProductId <= 0) {
                $this->importErrors[] = [
                    'sheet' => 'occasion_products',
                    'row' => $index + 2,
                    'occasion_id' => $excelOccasionId,
                    'errors' => ['Invalid occasion ID or product ID']
                ];
                continue;
            }

            if (!isset($this->occasionMap[$excelOccasionId])) {
                continue;
            }

            if (!isset($this->productMap[$excelProductId])) {
                continue;
            }

            $dbOccasionId = $this->occasionMap[$excelOccasionId];
            $dbProductId = $this->productMap[$excelProductId];

            $product = Product::whereNull('deleted_at')->find($dbProductId);
            if (!$product) {
                continue;
            }

            $dbVariantId = null;
            if ($excelVariantId && isset($this->variantMap[$excelVariantId])) {
                $dbVariantId = $this->variantMap[$excelVariantId];
                $variant = ProductVariant::find($dbVariantId);
                if (!$variant) {
                    continue;
                }
            }

            if (!$dbVariantId) {
                continue;
            }

            OccasionProduct::updateOrCreate(
                [
                    'occasion_id' => $dbOccasionId,
                    'vendor_product_variant_id' => $dbVariantId
                ],
                [
                    'special_price' => $this->normalizeDecimal($row['special_price'] ?? 0),
                    'position' => (int)($row['position'] ?? 0)
                ]
            );
        }
    }

    private function normalizeDecimal($value): float
    {
        return (float)($value ?? 0);
    }
}
