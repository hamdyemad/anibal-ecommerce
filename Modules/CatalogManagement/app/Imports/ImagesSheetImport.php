<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\Product;
use App\Models\Attachment;

/**
 * Sheet: images
 * - product_id from excel -> lookup in productMap
 * - upsert images (no soft delete checks)
 */
class ImagesSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function __construct(
        protected array &$productMap,
        protected array &$importErrors = []
    ) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelProductId = (int)($row['product_id'] ?? 0);
            $imageUrl = trim((string)($row['image'] ?? ''));

            // Validate row data
            $validator = Validator::make($row->toArray(), [
                'product_id' => 'required|integer|min:1',
                'image' => 'required|string',
                'is_main' => 'nullable|in:0,1,true,false,yes,no',
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'images',
                    'row' => $index + 2,
                    'product_id' => $excelProductId,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($excelProductId <= 0 || $imageUrl === '') {
                $this->importErrors[] = [
                    'sheet' => 'images',
                    'row' => $index + 2,
                    'product_id' => $excelProductId,
                    'errors' => ['Invalid product ID or image URL']
                ];
                continue;
            }

            // Skip if product not in map (soft-deleted or not imported)
            if (!isset($this->productMap[$excelProductId])) {
                continue;
            }

            $dbProductId = $this->productMap[$excelProductId];

            // Check if product exists and is not soft-deleted
            $product = Product::whereNull('deleted_at')->find($dbProductId);
            if (!$product) {
                continue;
            }

            // Determine if this is main image or additional
            $isMain = in_array(
                strtolower(trim((string)($row['is_main'] ?? '0'))),
                ['1', 'true', 'yes'],
                true
            );

            if ($isMain) {
                // Update main product image using morphOne relationship
                $product->mainImage()->updateOrCreate(
                    [
                        'attachable_id' => $dbProductId,
                        'attachable_type' => Product::class,
                        'type' => 'main_image'
                    ],
                    [
                        'path' => $imageUrl
                    ]
                );
            } else {
                // Upsert additional image using morphMany relationship
                Attachment::updateOrCreate(
                    [
                        'attachable_id' => $dbProductId,
                        'attachable_type' => Product::class,
                        'type' => 'additional_image',
                        'path' => $imageUrl
                    ],
                    [
                        'path' => $imageUrl
                    ]
                );
            }
        }
    }
}
