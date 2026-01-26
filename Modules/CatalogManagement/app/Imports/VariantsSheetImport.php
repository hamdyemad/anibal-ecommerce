<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use App\Models\ActivityLog;

/**
 * Sheet: variants
 * Creates VendorProductVariant entries
 */
class VariantsSheetImport implements ToCollection, WithHeadingRow, SkipsOnError, WithChunkReading
{
    use SkipsErrors;

    protected array $variantSkus = [];
    protected array $processedVariantsByProduct = []; // Track which variants were processed for each product

    public function __construct(
        protected array &$vendorProductMap,
        protected array &$variantMap,
        protected array &$importErrors = [],
        protected bool $isAdmin = false
    ) {}

    public function collection(Collection $rows)
    {
        $rowCounter = 0;
        foreach ($rows as $index => $row) {
            $rowCounter++;
            $productSku = trim((string)($row['product_sku'] ?? ''));
            $variantSku = $this->normalizeSku($row['sku'] ?? '');
            
            // Normalize SKU in the row data for validation
            $rowData = $row->toArray();
            $rowData['sku'] = $variantSku;

            $validator = Validator::make($rowData, [
                'sku' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'variant_configuration_id' => 'nullable|integer|exists:variants_configurations,id',
                'has_discount' => 'nullable|in:0,1,true,false,yes,no',
                'price_before_discount' => 'nullable|numeric|min:0',
                'discount_end_date' => 'nullable',
            ], [
                'sku.required' => __('validation.required', ['attribute' => 'sku']),
                'price.required' => __('validation.required', ['attribute' => 'price']),
                'price.numeric' => __('validation.numeric', ['attribute' => 'price']),
                'variant_configuration_id.integer' => __('validation.integer', ['attribute' => 'variant_configuration_id']),
                'variant_configuration_id.exists' => __('validation.exists', ['attribute' => 'variant_configuration_id']),
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $variantSku,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($productSku === '' || $variantSku === '') {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $variantSku,
                    'errors' => [__('catalogmanagement::product.invalid_product_sku_or_variant_sku')]
                ];
                continue;
            }

            // Check for duplicate SKU in Excel
            if (isset($this->variantSkus[$variantSku])) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $variantSku,
                    'errors' => [__('catalogmanagement::product.duplicate_variant_sku_in_excel', ['row' => $this->variantSkus[$variantSku]])]
                ];
                continue;
            }

            // Check if product SKU exists in the map
            if (!isset($this->vendorProductMap[$productSku])) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $variantSku,
                    'errors' => [__('catalogmanagement::product.product_not_found_or_skipped')]
                ];
                continue;
            }

            $vendorProductId = $this->vendorProductMap[$productSku];
            $vendorProduct = VendorProduct::find($vendorProductId);
            
            if (!$vendorProduct) {
                continue;
            }

            // Track this variant as processed for this product
            if (!isset($this->processedVariantsByProduct[$vendorProductId])) {
                $this->processedVariantsByProduct[$vendorProductId] = [];
            }

            // Check if variant SKU already exists - if so, update instead of creating new
            $existingVariant = VendorProductVariant::where('sku', $variantSku)->first();
                
            if ($existingVariant) {
                // For vendors, only allow updating variants of their own products
                if (!$this->isAdmin) {
                    $existingVendorProduct = $existingVariant->vendorProduct;
                    if ($existingVendorProduct && $existingVendorProduct->vendor_id != $vendorProduct->vendor_id) {
                        $this->importErrors[] = [
                            'sheet' => 'variants',
                            'row' => $index + 2,
                            'sku' => $variantSku,
                            'errors' => [__('catalogmanagement::product.variant_sku_belongs_to_another_vendor')]
                        ];
                        continue;
                    }
                }

                // Check if the variant belongs to the correct product
                // If product_id in Excel matches the existing variant's product, update it
                // Otherwise, this is a conflict - SKU exists but for a different product
                if ($existingVariant->vendor_product_id != $vendorProductId) {
                    // SKU exists but belongs to a different product
                    // For now, we'll update the variant to belong to the new product
                    // This allows moving variants between products via import
                    $existingVariant->vendor_product_id = $vendorProductId;
                }

                // Store old data for activity log
                $oldVariantData = $existingVariant->toArray();

                // Update existing variant
                $hasDiscount = $this->normalizeYesNo($row['has_discount'] ?? '0') === 'yes';
                $discountEndDate = $hasDiscount && !empty($row['discount_end_date']) 
                    ? $this->parseExcelDate($row['discount_end_date']) 
                    : null;
                
                $existingVariant->update([
                    'vendor_product_id' => $vendorProductId, // Update product association
                    'variant_configuration_id' => !empty($row['variant_configuration_id']) ? (int)$row['variant_configuration_id'] : $existingVariant->variant_configuration_id,
                    'price' => $this->normalizeDecimal($row['price'] ?? $existingVariant->price),
                    'has_discount' => $hasDiscount,
                    'price_before_discount' => $hasDiscount ? $this->normalizeDecimal($row['price_before_discount'] ?? 0) : 0,
                    'discount_end_date' => $discountEndDate,
                ]);

                // Log activity for variant update
                $this->logBulkActivity('updated', $existingVariant, $oldVariantData, $existingVariant->fresh()->toArray());

                // Map to existing ID
                $this->variantMap[$variantSku] = (int)$existingVariant->id;
                $this->variantSkus[$variantSku] = $index + 2;
                
                // Track this variant as processed
                $this->processedVariantsByProduct[$vendorProductId][] = $existingVariant->id;
                continue;
            }

            $this->variantSkus[$variantSku] = $index + 2;

            $hasDiscount = $this->normalizeYesNo($row['has_discount'] ?? '0') === 'yes';
            $discountEndDate = $hasDiscount && !empty($row['discount_end_date']) 
                ? $this->parseExcelDate($row['discount_end_date']) 
                : null;

            $variant = VendorProductVariant::create([
                'vendor_product_id' => $vendorProductId,
                'variant_configuration_id' => !empty($row['variant_configuration_id']) ? (int)$row['variant_configuration_id'] : null,
                'sku' => $variantSku,
                'price' => $this->normalizeDecimal($row['price'] ?? 0),
                'has_discount' => $hasDiscount,
                'price_before_discount' => $hasDiscount ? $this->normalizeDecimal($row['price_before_discount'] ?? 0) : 0,
                'discount_end_date' => $discountEndDate,
            ]);

            // Map by SKU instead of Excel ID
            $this->variantMap[$variantSku] = (int)$variant->id;
            
            // Track this variant as processed
            $this->processedVariantsByProduct[$vendorProductId][] = $variant->id;
        }
        
        // After processing all rows, delete variants that weren't in the Excel file
        $this->deleteUnprocessedVariants();
    }

    private function normalizeYesNo($value): string
    {
        $v = strtolower(trim((string)$value));
        return in_array($v, ['1', 'true', 'yes', 'y'], true) ? 'yes' : 'no';
    }

    /**
     * Parse Excel date format to Y-m-d format
     * Handles both Excel numeric dates and string dates
     */
    private function parseExcelDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // If it's already a valid date string (Y-m-d format), return it
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // If it's a numeric Excel date (days since 1900-01-01)
        if (is_numeric($value)) {
            try {
                // Excel dates are stored as number of days since 1900-01-01
                // PHPSpreadsheet handles this conversion
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to parse Excel date: ' . $value);
                return null;
            }
        }

        // Try to parse as a date string
        try {
            $date = \Carbon\Carbon::parse($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to parse date string: ' . $value);
            return null;
        }
    }

    private function normalizeSku($value): string
    {
        $variantSku = trim((string)$value);
        $variantSku = preg_replace('/\s+/', ' ', $variantSku);
        return trim($variantSku);
    }

    private function normalizeDecimal($value): float
    {
        return (float)($value ?? 0);
    }

    /**
     * Delete variants that exist in the database but weren't in the Excel file
     * This ensures the Excel file is the source of truth for variants
     */
    private function deleteUnprocessedVariants(): void
    {
        foreach ($this->processedVariantsByProduct as $vendorProductId => $processedVariantIds) {
            // Get all existing variants for this product
            $existingVariants = VendorProductVariant::where('vendor_product_id', $vendorProductId)->get();
            
            foreach ($existingVariants as $variant) {
                // If this variant wasn't processed (not in Excel), delete it
                if (!in_array($variant->id, $processedVariantIds)) {
                    // Log activity for variant deletion
                    $this->logBulkActivity('deleted', $variant, $variant->toArray(), []);
                    
                    // Delete the variant (this will cascade delete stock entries if configured)
                    $variant->delete();
                }
            }
        }
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
                'deleted' => 'activity_log.deleted_model',
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
            } elseif ($action === 'deleted') {
                $properties = [
                    'old' => $oldData,
                    'source' => 'bulk_upload',
                    'reason' => 'Variant not present in Excel import file',
                ];
            }

            // Only log if there are actual changes or it's a create/delete action
            if ($action === 'created' || $action === 'deleted' || !empty($properties['new'])) {
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
            \Illuminate\Support\Facades\Log::error('Bulk import activity log error: ' . $e->getMessage());
        }
    }

    /**
     * Define chunk size for reading Excel file
     */
    public function chunkSize(): int
    {
        return 100;
    }
}

