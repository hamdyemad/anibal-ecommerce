<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\Product;

/**
 * Sheet: products
 * - demo/export: id is DB id (but we treat it as excel key)
 * - lookup by sku (active only)
 * - skip if sku exists soft-deleted in products (onlyTrashed)
 */
class ProductsSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected array $productSkus = []; // Track SKUs for uniqueness validation

    public function __construct(
        protected array &$productMap,
        protected array &$productSkuToDbId,
        protected array &$importErrors = [],
        protected array &$productsWithVariants = []
    ) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelId = (int)($row['id'] ?? 0);
            $sku     = $this->normalizeSku($row['sku'] ?? '');

            // Validate row data
            $validator = Validator::make($row->toArray(), [
                'id' => 'required|integer|min:1',
                'sku' => 'required|string|max:255',
                'title_en' => 'nullable|string|max:255',
                'title_ar' => 'nullable|string|max:255',
                'department' => 'nullable|integer|exists:departments,id',
                'main_category' => 'nullable|integer|exists:categories,id',
                'sub_category' => 'nullable|integer|exists:sub_categories,id',
                'brand' => 'nullable|integer|exists:brands,id',
                'status' => 'nullable|in:0,1,true,false,yes,no',
                'featured_product' => 'nullable|in:0,1,true,false,yes,no',
                'have_varient' => 'nullable|in:0,1,true,false,yes,no,yes,no',
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($excelId <= 0 || $sku === '') {
                $this->importErrors[] = [
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => ['Invalid ID or SKU']
                ];
                continue;
            }

            // Check for duplicate SKU in the Excel file
            if (isset($this->productSkus[$sku])) {
                $this->importErrors[] = [
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => ["Duplicate SKU in Excel file. First occurrence at row {$this->productSkus[$sku]}"]
                ];
                continue;
            }

            // Check if SKU already exists in database
            $existsInDb = Product::where('sku', $sku)->exists();
            if ($existsInDb) {
                $this->importErrors[] = [
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => ['SKU already exists in database']
                ];
                continue;
            }

            // Mark this SKU as seen
            $this->productSkus[$sku] = $index + 2;

            // Track if product has variants
            $hasVariants = $this->normalizeYesNo($row['have_varient'] ?? '') === 'yes';
            if ($hasVariants) {
                $this->productsWithVariants[$excelId] = [
                    'row' => $index + 2,
                    'sku' => $sku
                ];
            }

            // cache hit
            if (isset($this->productSkuToDbId[$sku])) {
                $this->productMap[$excelId] = (int)$this->productSkuToDbId[$sku];
                continue;
            }

            // SKIP if product is soft-deleted
            if (Product::onlyTrashed()->where('sku', $sku)->exists()) {
                continue;
            }

            // active product only
            $product = Product::where('sku', $sku)->whereNull('deleted_at')->first();

            if (!$product) {
                // Determine vendor_id
                $vendorId = null;
                if (isset($row['vendor_id']) && !empty($row['vendor_id'])) {
                    // Admin uploaded file with vendor_id column
                    $vendorId = $this->normalizeNullableInt($row['vendor_id']);
                } elseif (Auth::check() && in_array(Auth::user()->user_type_id, \App\Models\UserType::vendorIds())) {
                    // Vendor user - use their vendor_id
                    $vendorId = Auth::user()->vendor?->id;
                }

                $product = Product::create([
                    'sku'            => $sku,
                    'title_en'       => $this->nullIfEmpty($row['title_en'] ?? null),
                    'title_ar'       => $this->nullIfEmpty($row['title_ar'] ?? null),
                    'description_en' => $this->nullIfEmpty($row['description_en'] ?? null),
                    'description_ar' => $this->nullIfEmpty($row['description_ar'] ?? null),
                    'meta_title_en'  => $this->nullIfEmpty($row['meta_title_en'] ?? null),
                    'meta_title_ar'  => $this->nullIfEmpty($row['meta_title_ar'] ?? null),
                    'keywords_en'    => $this->nullIfEmpty($row['keywords_en'] ?? null),
                    'keywords_ar'    => $this->nullIfEmpty($row['keywords_ar'] ?? null),
                    'details_en'     => $this->nullIfEmpty($row['details_en'] ?? null),
                    'details_ar'     => $this->nullIfEmpty($row['details_ar'] ?? null),
                    'summary_en'     => $this->nullIfEmpty($row['summary_en'] ?? null),
                    'summary_ar'     => $this->nullIfEmpty($row['summary_ar'] ?? null),
                    'featured_product' => in_array(
                        strtolower(trim((string)($row['featured_product'] ?? '0'))),
                        ['1', 'true', 'yes'],
                        true
                    ) ? '1' : '0',
                    'size_color_type'  => ($this->normalizeYesNo($row['have_varient'] ?? '') === 'yes')
                        ? 'with_variants'
                        : 'without_any',
                    'department_id'    => $this->normalizeNullableInt($row['department'] ?? null),
                    'main_category_id' => $this->normalizeNullableInt($row['main_category'] ?? null),
                    'sub_category_id'  => $this->normalizeNullableInt($row['sub_category'] ?? null),
                    'brand_id'         => $this->normalizeNullableInt($row['brand'] ?? null),
                    'vendor_id'        => $vendorId,
                    'status' => in_array(
                        strtolower(trim((string)($row['status'] ?? '0'))),
                        ['1', 'true', 'yes'],
                        true
                    ) ? '1' : '0',
                    'admin_id' => Auth::check() ? Auth::id() : 1,
                    'stock'  => 0,
                    'slug_en' => Str::slug($row['title_en'] ?? Str::random()),
                    'slug_ar' => Str::slug($row['title_ar'] ?? Str::random()),
                    'image'  => $this->nullIfEmpty($row['image'] ?? null),
                    'points' => (int)($row['points'] ?? 0),
                ]);
            }

            $this->productMap[$excelId] = (int)$product->id;
            $this->productSkuToDbId[$sku] = (int)$product->id;
        }
    }

    private function normalizeYesNo($value): string
    {
        $v = strtolower(trim((string)$value));
        return in_array($v, ['1', 'true', 'yes', 'y'], true) ? 'yes' : 'no';
    }

    private function normalizeSku($value): string
    {
        $sku = trim((string)$value);
        $sku = preg_replace('/\s+/', ' ', $sku);
        return trim($sku);
    }

    private function normalizeNullableInt($value): ?int
    {
        $n = (int)($value ?? 0);
        return $n > 0 ? $n : null;
    }

    private function nullIfEmpty($value): ?string
    {
        $v = trim((string)$value);
        return $v === '' ? null : $v;
    }
}
