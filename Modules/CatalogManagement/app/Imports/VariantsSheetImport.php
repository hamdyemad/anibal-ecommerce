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

/**
 * Sheet: variants
 * - product_id from excel -> lookup in productMap
 * - upsert variants by sku (no soft delete checks)
 * - build variantMap and variantSkuToDbId for stock sheet
 */
class VariantsSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected array $variantSkus = [];

    public function __construct(
        protected array &$productMap,
        protected array &$variantMap,
        protected array &$variantSkuToDbId,
        protected array &$importErrors = []
    ) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelProductId = (int)($row['product_id'] ?? 0);
            $excelVariantId = (int)($row['id'] ?? 0);
            $sku = $this->normalizeSku($row['sku'] ?? '');

            $validator = Validator::make($row->toArray(), [
                'id' => 'required|integer|min:1',
                'product_id' => 'required|integer|min:1',
                'sku' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'variant_configuration_id' => 'nullable|integer|exists:variants_configurations,id',
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($excelProductId <= 0 || $excelVariantId <= 0 || $sku === '') {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => ['Invalid product ID, variant ID, or SKU']
                ];
                continue;
            }

            if (isset($this->variantSkus[$sku])) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => ["Duplicate variant SKU in Excel file. First occurrence at row {$this->variantSkus[$sku]}"]
                ];
                continue;
            }

            $existsInDb = ProductVariant::where('sku', $sku)->exists();
            if ($existsInDb) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => ['Variant SKU already exists in database']
                ];
                continue;
            }

            $this->variantSkus[$sku] = $index + 2;

            if (!isset($this->productMap[$excelProductId])) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => ['Product not found or was skipped during import']
                ];
                continue;
            }

            $dbProductId = $this->productMap[$excelProductId];
            $product = Product::whereNull('deleted_at')->find($dbProductId);
            if (!$product) {
                continue;
            }

            $variantKey = "{$dbProductId}|{$sku}";

            if (isset($this->variantSkuToDbId[$variantKey])) {
                $this->variantMap[$excelVariantId] = (int)$this->variantSkuToDbId[$variantKey];
                continue;
            }

            $variant = ProductVariant::where('product_id', $dbProductId)
                ->where('sku', $sku)
                ->first();

            if ($variant) {
                $variant->price = $this->normalizeDecimal($row['price'] ?? 0);
                $variant->save();
            } else {
                $variant = ProductVariant::create([
                    'product_id' => $dbProductId,
                    'sku' => $sku,
                    'price' => $this->normalizeDecimal($row['price'] ?? 0),
                    'stock' => 0,
                    'variant_configuration_id' => $this->normalizeNullableInt($row['variant_configuration_id'] ?? null),
                ]);
            }

            $this->variantMap[$excelVariantId] = (int)$variant->id;
            $this->variantSkuToDbId[$variantKey] = (int)$variant->id;
        }
    }

    private function normalizeSku($value): string
    {
        $sku = trim((string)$value);
        $sku = preg_replace('/\s+/', ' ', $sku);
        return trim($sku);
    }

    private function normalizeDecimal($value): float
    {
        return (float)($value ?? 0);
    }

    private function normalizeNullableInt($value): ?int
    {
        $n = (int)($value ?? 0);
        return $n > 0 ? $n : null;
    }
}
