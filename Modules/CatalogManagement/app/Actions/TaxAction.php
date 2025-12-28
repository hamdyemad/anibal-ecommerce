<?php

namespace Modules\CatalogManagement\app\Actions;

use App\Services\LanguageService;
use Modules\CatalogManagement\app\Services\TaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Interfaces\TaxRepositoryInterface;

class TaxAction {

    public function __construct(
        protected LanguageService $languageService,
        protected TaxService $taxService,
        protected TaxRepositoryInterface $taxRepositoryInterface
    ) {}

    /**
     * Get datatable data for taxes
     */
    public function getDatatableData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $searchValue = $request->get('search')['value'] ?? '';
        $orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
        $orderDirection = $request->get('order')[0]['dir'] ?? 'asc';

        // Get filter parameters
        $isActive = $request->get('is_active');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'is_active' => $isActive,
        ];

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total and filtered counts
        $totalRecords = $this->taxRepositoryInterface->getTaxesQuery([])->count();
        $filteredRecords = $this->taxRepositoryInterface->getTaxesQuery($filters)->count();

        // Determine sort column
        $orderBy = null;
        switch ($orderColumnIndex) {
            case 0:
                $orderBy = 'id';
                break;
            case 1:
                $orderBy = null; // Name - handled by translations
                break;
            case 2:
                $orderBy = 'percentage';
                break;
            case 3:
                $orderBy = 'is_active';
                break;
            case 4:
                $orderBy = 'created_at';
                break;
        }

        // Calculate pagination
        $perPage = $length > 0 ? $length : 10;
        $page = ($start / $perPage) + 1;

        // Get taxes with pagination and sorting
        $taxesQuery = $this->taxRepositoryInterface->getTaxesQuery($filters, $orderBy, $orderDirection);
        $taxes = $taxesQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = $this->formatDataForDataTables($taxes, $languages);

        return [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ];
    }

    /**
     * Format data for DataTables
     */
    protected function formatDataForDataTables($taxes, $languages)
    {
        $data = [];

        foreach ($taxes as $index => $tax) {
            $row = [
                'index' => $taxes->firstItem() + $index,
                'id' => $tax->id,
                'names' => [],
                'percentage' => $tax->percentage,
                'is_active' => $tax->is_active,
                'created_at' => $tax->created_at,
                'display_name' => '',
            ];

            // Get names for each language
            foreach ($languages as $language) {
                $translation = $tax->translations()
                    ->where('lang_id', $language->id)
                    ->where('lang_key', 'name')
                    ->first();

                $name = $translation ? $translation->lang_value : '-';
                $row['names'][$language->id] = [
                    'value' => $name,
                    'rtl' => $language->rtl
                ];

                // Set display name (first available translation)
                if (!$row['display_name'] && $name !== '-') {
                    $row['display_name'] = $name;
                }
            }

            $data[] = $row;
        }

        return $data;
    }
}
