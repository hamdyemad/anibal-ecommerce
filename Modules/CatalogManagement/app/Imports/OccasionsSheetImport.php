<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\Occasion;

/**
 * Sheet: occasions
 * - upsert occasions by id (no soft delete checks)
 * - build occasionMap for occasion_products sheet
 */
class OccasionsSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function __construct(
        protected array &$occasionMap,
        protected array &$importErrors = []
    ) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelId = (int)($row['id'] ?? 0);
            $nameEn = trim((string)($row['name_en'] ?? ''));
            $nameAr = trim((string)($row['name_ar'] ?? ''));

            // Validate row data
            $validator = Validator::make($row->toArray(), [
                'id' => 'required|integer|min:1',
                'name_en' => 'nullable|string|max:255',
                'name_ar' => 'nullable|string|max:255',
                'is_active' => 'nullable|in:0,1,true,false,yes,no',
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'occasions',
                    'row' => $index + 2,
                    'id' => $excelId,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($excelId <= 0 || ($nameEn === '' && $nameAr === '')) {
                $this->importErrors[] = [
                    'sheet' => 'occasions',
                    'row' => $index + 2,
                    'id' => $excelId,
                    'errors' => ['Invalid ID or both names are empty']
                ];
                continue;
            }

            // Upsert occasion
            $occasion = Occasion::updateOrCreate(
                ['id' => $excelId],
                [
                    'name_en' => $nameEn ?: null,
                    'name_ar' => $nameAr ?: null,
                    'is_active' => in_array(
                        strtolower(trim((string)($row['is_active'] ?? '1'))),
                        ['1', 'true', 'yes'],
                        true
                    ) ? 1 : 0,
                ]
            );

            // Map excel occasion id to db occasion id
            $this->occasionMap[$excelId] = (int)$occasion->id;
        }
    }
}
