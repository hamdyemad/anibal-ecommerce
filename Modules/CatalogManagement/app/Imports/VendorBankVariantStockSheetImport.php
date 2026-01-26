<?php

namespace Modules\CatalogManagement\app\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Import variant_stock sheet for vendor bank products
 * Updates stock quantities for existing variants
 */
class VendorBankVariantStockSheetImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected int $vendorId;
    protected int $userId;
    protected $parentImport;
    protected int $currentRow = 1;

    public function __construct(int $vendorId, int $userId, $parentImport)
    {
        $this->vendorId = $vendorId;
        $this->userId = $userId;
        $this->parentImport = $parentImport;
    }

    public function model(array $row)
    {
        $this->currentRow++;

        try {
            // Validate required fields
            $validator = Validator::make($row, [
                'variant_sku' => 'required|string',
                'region_id' => 'required|integer',
                'stock' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                $this->parentImport->addError('variant_stock', $this->currentRow, $row['variant_sku'] ?? 'N/A', $validator->errors()->all());
                return null;
            }

            // Find the variant by SKU and vendor
            $variant = VendorProductVariant::whereHas('vendorProduct', function($q) {
                $q->where('vendor_id', $this->vendorId)
                  ->whereHas('product', function($pq) {
                      $pq->where('type', 'bank');
                  });
            })
            ->where('sku', $row['variant_sku'])
            ->first();

            if (!$variant) {
                $this->parentImport->addError('variant_stock', $this->currentRow, $row['variant_sku'], 'Variant not found or not a bank product');
                return null;
            }

            // Update or create stock for this region
            $stock = VendorProductVariantStock::updateOrCreate(
                [
                    'vendor_product_variant_id' => $variant->id,
                    'region_id' => $row['region_id'],
                ],
                [
                    'quantity' => $row['stock'],
                ]
            );

            Log::info('Vendor bank variant stock updated', [
                'vendor_id' => $this->vendorId,
                'variant_sku' => $row['variant_sku'],
                'region_id' => $row['region_id'],
                'stock' => $row['stock']
            ]);

            return null; // We already saved, return null to prevent duplicate save

        } catch (\Exception $e) {
            Log::error('Error importing vendor bank variant stock', [
                'row' => $this->currentRow,
                'variant_sku' => $row['variant_sku'] ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            
            $this->parentImport->addError('variant_stock', $this->currentRow, $row['variant_sku'] ?? 'N/A', $e->getMessage());
            return null;
        }
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
