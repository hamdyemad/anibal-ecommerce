<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\VendorProduct;
use App\Models\ActivityLog;

/**
 * Sheet: products
 * Creates Product (bank) and VendorProduct entries
 */
class ProductsSheetImport implements ToCollection, WithHeadingRow, SkipsOnError, WithChunkReading
{
    use SkipsErrors;

    protected array $vendorProductSkus = []; // Track SKUs for uniqueness validation

    public function __construct(
        protected array &$productMap,
        protected array &$vendorProductMap,
        protected array &$importErrors = [],
        protected array &$productsWithVariants = [],
        protected bool $isAdmin = false
    ) {}

    public function collection(Collection $rows)
    {
        $currentUser = Auth::user();
        
        foreach ($rows as $index => $row) {
            $sku = $this->normalizeSku($row['sku'] ?? '');
            
            // Determine vendor_id per row
            $vendorId = null;
            if (isVendor()) {
                $vendorId = $currentUser->vendor?->id;
            } elseif (isAdmin()) {
                // For admin: check vendor_id column in the row
                // Try multiple possible column name variations
                $vendorId = $this->getVendorIdFromRow($row);
            }
            
            // Normalize SKU in the row data for validation
            $rowData = $row->toArray();
            $rowData['sku'] = $sku;

            // Validate row data
            $validator = Validator::make($rowData, [
                'sku' => 'required|string|max:255',
                'title_en' => 'nullable|string|max:255',
                'title_ar' => 'nullable|string|max:255',
                'department' => 'required|integer|exists:departments,id',
                'main_category' => 'required|integer|exists:categories,id',
                'sub_category' => 'nullable|integer|exists:sub_categories,id',
                'brand' => 'nullable|integer|exists:brands,id',
                'status' => 'nullable|in:0,1,true,false,yes,no',
                'featured_product' => 'nullable|in:0,1,true,false,yes,no',
                'have_varient' => 'nullable|in:0,1,true,false,yes,no',
                'max_per_order' => 'nullable|integer|min:1',
            ], [
                'sku.required' => __('validation.required', ['attribute' => 'sku']),
                'sku.string' => __('validation.string', ['attribute' => 'sku']),
                'sku.max' => __('validation.max.string', ['attribute' => 'sku', 'max' => 255]),
                'department.required' => __('validation.required', ['attribute' => 'department']),
                'department.integer' => __('validation.integer', ['attribute' => 'department']),
                'department.exists' => __('validation.exists', ['attribute' => 'department']),
                'main_category.required' => __('validation.required', ['attribute' => 'main_category']),
                'main_category.integer' => __('validation.integer', ['attribute' => 'main_category']),
                'main_category.exists' => __('validation.exists', ['attribute' => 'main_category']),
                'sub_category.integer' => __('validation.integer', ['attribute' => 'sub_category']),
                'sub_category.exists' => __('catalogmanagement::product.sub_category_invalid'),
                'brand.integer' => __('validation.integer', ['attribute' => 'brand']),
                'brand.exists' => __('catalogmanagement::product.brand_invalid'),
                'max_per_order.integer' => __('validation.integer', ['attribute' => 'max_per_order']),
                'max_per_order.min' => __('validation.min.numeric', ['attribute' => 'max_per_order', 'min' => 1]),
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'products',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            // Additional validation: Check category relationships
            $departmentId = (int)($row['department'] ?? 0);
            $mainCategoryId = (int)($row['main_category'] ?? 0);
            $subCategoryId = $this->normalizeNullableInt($row['sub_category'] ?? null);

            // Validate main_category belongs to department
            if ($mainCategoryId > 0 && $departmentId > 0) {
                $mainCategory = \Modules\CategoryManagment\app\Models\Category::find($mainCategoryId);
                if (!$mainCategory || $mainCategory->department_id != $departmentId) {
                    $this->importErrors[] = [
                        'sheet' => 'products',
                        'row' => $index + 2,
                        'sku' => $sku,
                        'errors' => [__('catalogmanagement::product.main_category_not_belong_to_department')]
                    ];
                    continue;
                }
            }

            // Validate sub_category belongs to main_category (sub_category is in the same Category model with parent_id)
            if ($subCategoryId > 0 && $mainCategoryId > 0) {
                $subCategory = \Modules\CategoryManagment\app\Models\SubCategory::find($subCategoryId);
                if (!$subCategory || $subCategory->category_id != $mainCategoryId) {
                    $this->importErrors[] = [
                        'sheet' => 'products',
                        'row' => $index + 2,
                        'sku' => $sku,
                        'errors' => [__('catalogmanagement::product.sub_category_not_belong_to_main_category')]
                    ];
                    continue;
                }
            }

            if ($sku === '') {
                $this->importErrors[] = [
                    'sheet' => 'products',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => [__('catalogmanagement::product.invalid_sku')]
                ];
                continue;
            }

            // Check for duplicate SKU in the Excel file
            if (isset($this->vendorProductSkus[$sku])) {
                $this->importErrors[] = [
                    'sheet' => 'products',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => [__('catalogmanagement::product.duplicate_sku_in_excel', ['row' => $this->vendorProductSkus[$sku]])]
                ];
                continue;
            }

            if (!$vendorId) {
                $this->importErrors[] = [
                    'sheet' => 'products',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => [__('catalogmanagement::product.vendor_id_required') . ' (Column B is empty or vendor_id not found)']
                ];
                continue;
            }

            // Get vendor's country_id
            $vendor = \Modules\Vendor\app\Models\Vendor::find($vendorId);
            if (!$vendor) {
                $this->importErrors[] = [
                    'sheet' => 'products',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => [__('catalogmanagement::product.vendor_not_found')]
                ];
                continue;
            }

            $vendorCountryId = $vendor->country_id;

            // Validate department belongs to vendor's country
            if ($departmentId > 0 && $vendorCountryId) {
                $department = \Modules\CategoryManagment\app\Models\Department::find($departmentId);
                if (!$department || $department->country_id != $vendorCountryId) {
                    $this->importErrors[] = [
                        'sheet' => 'products',
                        'row' => $index + 2,
                        'sku' => $sku,
                        'errors' => [__('catalogmanagement::product.department_not_belong_to_vendor_country')]
                    ];
                    continue;
                }
            }

            // Check if SKU already exists - if so, update instead of creating new
            $existingVendorProduct = VendorProduct::where('sku', $sku)->first();
                
            if ($existingVendorProduct) {
                // For vendors, only allow updating their own products
                if (!isAdmin() && $existingVendorProduct->vendor_id != $vendorId) {
                    $this->importErrors[] = [
                        'sheet' => 'products',
                        'row' => $index + 2,
                        'sku' => $sku,
                        'errors' => [__('catalogmanagement::product.sku_belongs_to_another_vendor')]
                    ];
                    continue;
                }

                // Update existing product
                $product = $existingVendorProduct->product;
                
                if ($product) {
                    $oldProductData = $product->toArray();
                    
                    // Track if product has variants
                    $hasVariants = $this->normalizeYesNo($row['have_varient'] ?? '') === 'yes';
                    
                    $product->update([
                        'brand_id' => $this->normalizeNullableInt($row['brand'] ?? null) ?: $product->brand_id,
                        'department_id' => $this->normalizeNullableInt($row['department'] ?? null) ?: $product->department_id,
                        'category_id' => $this->normalizeNullableInt($row['main_category'] ?? null) ?: $product->category_id,
                        'sub_category_id' => $this->normalizeNullableInt($row['sub_category'] ?? null),
                        'configuration_type' => $hasVariants ? 'variants' : 'simple',
                    ]);

                    // Update translations
                    $this->updateTranslations($product, $row);
                    
                    // Log activity for product update
                    $this->logBulkActivity('updated', $product, $oldProductData, $product->fresh()->toArray());
                }

                // Update vendor product
                $oldVendorProductData = $existingVendorProduct->toArray();
                
                $existingVendorProduct->update([
                    'max_per_order' => (int)($row['max_per_order'] ?? $existingVendorProduct->max_per_order),
                    'is_active' => $this->normalizeYesNo($row['status'] ?? '1') === 'yes',
                    'is_featured' => $this->normalizeYesNo($row['featured_product'] ?? '0') === 'yes',
                ]);
                
                // Log activity for vendor product update
                $this->logBulkActivity('updated', $existingVendorProduct, $oldVendorProductData, $existingVendorProduct->fresh()->toArray());

                // Map SKU to database IDs (use SKU as key instead of Excel ID)
                $this->productMap[$sku] = (int)$product->id;
                $this->vendorProductMap[$sku] = (int)$existingVendorProduct->id;
                
                // Mark this SKU as seen
                $this->vendorProductSkus[$sku] = $index + 2;
                continue;
            }

            // Mark this SKU as seen
            $this->vendorProductSkus[$sku] = $index + 2;

            // Track if product has variants
            $hasVariants = $this->normalizeYesNo($row['have_varient'] ?? '') === 'yes';
            if ($hasVariants) {
                $this->productsWithVariants[$sku] = [
                    'row' => $index + 2,
                    'sku' => $sku
                ];
            }

            // Set status based on user role
            // Admin uploads: approved
            // Vendor uploads: pending
            $status = isAdmin() ? 'approved' : 'pending';

            // Create or get Product (bank product)
            $product = Product::create([
                'slug' => Str::slug($row['title_en'] ?? '') ?: Str::uuid(),
                'is_active' => true,
                'configuration_type' => $hasVariants ? 'variants' : 'simple',
                'vendor_id' => null, // Bank products don't have vendor_id
                'brand_id' => $this->normalizeNullableInt($row['brand'] ?? null),
                'department_id' => $this->normalizeNullableInt($row['department'] ?? null),
                'category_id' => $this->normalizeNullableInt($row['main_category'] ?? null),
                'sub_category_id' => $this->normalizeNullableInt($row['sub_category'] ?? null),
                'created_by_user_id' => $currentUser->id,
            ]);

            // Store translations
            $this->storeTranslations($product, $row);

            // Create VendorProduct
            $vendorProduct = VendorProduct::create([
                'vendor_id' => $vendorId,
                'product_id' => $product->id,
                'sku' => $sku,
                'max_per_order' => (int)($row['max_per_order'] ?? 1),
                'offer_date_view' => false,
                'is_active' => $this->normalizeYesNo($row['status'] ?? '1') === 'yes',
                'is_featured' => $this->normalizeYesNo($row['featured_product'] ?? '0') === 'yes',
                'status' => $status,
            ]);

            // Map SKU to database IDs (use SKU as key instead of Excel ID)
            $this->productMap[$sku] = (int)$product->id;
            $this->vendorProductMap[$sku] = (int)$vendorProduct->id;
        }
    }

    private function storeTranslations(Product $product, $row): void
    {
        $languages = \App\Models\Language::all();
        
        foreach ($languages as $language) {
            $langCode = $language->code;
            
            $fields = [
                'title' => $row["title_{$langCode}"] ?? null,
                'details' => $row["description_{$langCode}"] ?? null,
                'summary' => $row["summary_{$langCode}"] ?? null,
                'features' => $row["features_{$langCode}"] ?? null,
                'instructions' => $row["instructions_{$langCode}"] ?? null,
                'extra_description' => $row["extra_description_{$langCode}"] ?? null,
                'material' => $row["material_{$langCode}"] ?? null,
                'tags' => $row["tags_{$langCode}"] ?? null,
                'meta_title' => $row["meta_title_{$langCode}"] ?? null,
                'meta_description' => $row["meta_description_{$langCode}"] ?? null,
                'meta_keywords' => $row["meta_keywords_{$langCode}"] ?? null,
            ];

            foreach ($fields as $key => $value) {
                if (!empty($value)) {
                    $product->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => $key,
                        'lang_value' => $value,
                    ]);
                }
            }
        }
    }

    private function updateTranslations(Product $product, $row): void
    {
        $languages = \App\Models\Language::all();
        
        foreach ($languages as $language) {
            $langCode = $language->code;
            
            $fields = [
                'title' => $row["title_{$langCode}"] ?? null,
                'details' => $row["description_{$langCode}"] ?? null,
                'summary' => $row["summary_{$langCode}"] ?? null,
                'features' => $row["features_{$langCode}"] ?? null,
                'instructions' => $row["instructions_{$langCode}"] ?? null,
                'extra_description' => $row["extra_description_{$langCode}"] ?? null,
                'material' => $row["material_{$langCode}"] ?? null,
                'tags' => $row["tags_{$langCode}"] ?? null,
                'meta_title' => $row["meta_title_{$langCode}"] ?? null,
                'meta_description' => $row["meta_description_{$langCode}"] ?? null,
                'meta_keywords' => $row["meta_keywords_{$langCode}"] ?? null,
            ];

            foreach ($fields as $key => $value) {
                if (!empty($value)) {
                    // Update or create translation
                    $product->translations()->updateOrCreate(
                        [
                            'lang_id' => $language->id,
                            'lang_key' => $key,
                        ],
                        [
                            'lang_value' => $value,
                        ]
                    );
                }
            }
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

    /**
     * Log activity for bulk import operations
     */
    private function logBulkActivity(string $action, $model, array $oldData = [], array $newData = []): void
    {
        try {
            $modelName = class_basename($model);
            $identifier = $model->id;
            
            $descriptionKeys = [
                'created' => 'activity_log.created_model',
                'updated' => 'activity_log.updated_model',
            ];

            $properties = [];
            if ($action === 'updated' && !empty($oldData) && !empty($newData)) {
                // Get only changed values
                $changes = array_diff_assoc($newData, $oldData);
                $oldValues = array_intersect_key($oldData, $changes);
                
                if (!empty($changes)) {
                    $properties = [
                        'old' => $oldValues,
                        'new' => $changes,
                        'source' => 'bulk_upload',
                    ];
                }
            } elseif ($action === 'created') {
                $properties = [
                    'source' => 'bulk_upload',
                ];
            }

            // Only log if there are actual changes or it's a create action
            if ($action === 'created' || !empty($properties['new'])) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'description_key' => $descriptionKeys[$action] ?? null,
                    'description_params' => [
                        'model' => $modelName,
                        'identifier' => $identifier,
                    ],
                    'properties' => $properties,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'country_id' => session('country_id'),
                ]);
            }
        } catch (\Exception $e) {
            // Silent fail - don't break import for logging errors
            Log::error('Bulk import activity log error: ' . $e->getMessage());
        }
    }

    /**
     * Define chunk size for reading Excel file
     * Process 100 rows at a time for better memory management
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get vendor_id from row, trying multiple possible column name variations
     */
    protected function getVendorIdFromRow($row): ?int
    {
        // Try different possible column names that Laravel Excel might generate
        $possibleKeys = [
            'vendor_id',      // Standard format
            'vendorid',       // Without underscore
            'vendor id',      // With space
            'Vendor ID',      // Original case
            'Vendor Id',      // Title case
            'VENDOR_ID',      // Uppercase
        ];

        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && !empty($row[$key])) {
                return (int)$row[$key];
            }
        }

        // Log available keys for debugging (only once)
        static $logged = false;
        if (!$logged) {
            $logged = true;
            Log::warning('vendor_id not found in Excel row. Available columns: ' . implode(', ', array_keys($row->toArray())));
        }

        return null;
    }
}