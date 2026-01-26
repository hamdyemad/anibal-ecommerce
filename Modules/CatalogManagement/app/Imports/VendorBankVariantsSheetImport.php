<?php

namespace Modules\CatalogManagement\app\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Import variants sheet for vendor bank products
 * Updates pricing and discount information for existing variants
 */
class VendorBankVariantsSheetImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected int $vendorId;
    protected int $userId;
    protected $parentImport;
    protected int $currentRow = 1; // Start from 1 (header is row 1)

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
                'product_sku' => 'required|string',
                'sku' => 'required|string',
                'price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                $this->parentImport->addError('variants', $this->currentRow, $row['sku'] ?? 'N/A', $validator->errors()->all());
                return null;
            }

            // Find the variant by SKU and vendor
            $variant = VendorProductVariant::whereHas('vendorProduct', function($q) use ($row) {
                $q->where('vendor_id', $this->vendorId)
                  ->whereHas('product', function($pq) use ($row) {
                      $pq->where('type', 'bank')
                         ->where('sku', $row['product_sku']);
                  });
            })
            ->where('sku', $row['sku'])
            ->first();

            if (!$variant) {
                $this->parentImport->addError('variants', $this->currentRow, $row['sku'], 'Variant not found or not a bank product for product SKU: ' . $row['product_sku']);
                return null;
            }

            // Update variant pricing
            $variant->price = $row['price'];
            
            // Handle discount
            $hasDiscount = strtolower($row['has_discount'] ?? 'no') === 'yes';
            if ($hasDiscount && !empty($row['price_before_discount'])) {
                $variant->price_before_discount = $row['price_before_discount'];
                $variant->discount_end_date = !empty($row['discount_end_date']) ? $row['discount_end_date'] : null;
            } else {
                $variant->price_before_discount = 0;
                $variant->discount_end_date = null;
            }

            $variant->save();

            Log::info('Vendor bank variant updated', [
                'vendor_id' => $this->vendorId,
                'product_sku' => $row['product_sku'],
                'sku' => $row['sku'],
                'price' => $row['price']
            ]);

            return null; // We already saved, return null to prevent duplicate save

        } catch (\Exception $e) {
            Log::error('Error importing vendor bank variant', [
                'row' => $this->currentRow,
                'sku' => $row['sku'] ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            
            $this->parentImport->addError('variants', $this->currentRow, $row['sku'] ?? 'N/A', $e->getMessage());
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
