<?php

namespace Modules\CatalogManagement\app\Actions;

use Modules\CatalogManagement\app\Services\BundleCategoryService;
use App\Services\LanguageService;
use App\Traits\Res;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Interfaces\BundleCategoryRepositoryInterface;

class BundleCategoryAction
{
    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected BundleCategoryService $bundleCategoryService,
        protected BundleCategoryRepositoryInterface $bundleCategoryRepositoryInterface
    ) {}

    /**
     * Datatable endpoint for server-side processing
     */
    public function getDataTable($data)
    {
        try {
            // Get pagination parameters from DataTables
            $perPage = isset($data['length']) && $data['length'] > 0 ? (int)$data['length'] : 10;
            $start = isset($data['start']) && $data['start'] >= 0 ? (int)$data['start'] : 0;
            // Calculate page number from start offset
            $page = $perPage > 0 ? floor($start / $perPage) + 1 : 1;

            // Get sorting parameters
            $orderColumnIndex = $data['orderColumnIndex'] ?? 0;
            $orderDirection = $data['orderDirection'] ?? 'desc';

            Log::info('BundleCategoryAction - Sorting Parameters', [
                'orderColumnIndex' => $orderColumnIndex,
                'orderDirection' => $orderDirection,
                'all_data' => $data
            ]);

            // Get filter parameters
            $filters = [
                'search' => $data['search'] ?? null,
                'active' => $data['active'] ?? null,
                'created_date_from' => $data['created_date_from'] ?? null,
                'created_date_to' => $data['created_date_to'] ?? null,
            ];

            // Get languages
            $languages = $this->languageService->getAll();

            // Get total and filtered counts
            $totalRecords = $this->bundleCategoryRepositoryInterface->getBundleCategoriesQuery([])->count();
            $filteredRecords = $this->bundleCategoryRepositoryInterface->getBundleCategoriesQuery($filters)->count();

            // Determine sort column
            $orderBy = null;
            if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
                // Sorting by translated name column
                $languageIndex = $orderColumnIndex - 1;
                $selectedLanguage = $languages->values()->get($languageIndex);
                if ($selectedLanguage) {
                    $orderBy = [
                        'lang_id' => $selectedLanguage->id,
                        'key' => 'name'
                    ];
                }
            } elseif ($orderColumnIndex == count($languages) + 1) {
                $orderBy = 'active';
            } elseif ($orderColumnIndex == count($languages) + 2) {
                $orderBy = 'created_at';
            } else {
                // Default sorting
                $orderBy = 'id';
            }

            Log::info('BundleCategoryAction - Determined Sort', [
                'orderBy' => $orderBy,
                'orderDirection' => $orderDirection,
                'languagesCount' => count($languages)
            ]);

            // Get bundle categories with pagination and sorting
            $bundleCategoriesQuery = $this->bundleCategoryRepositoryInterface->getBundleCategoriesQuery($filters, $orderBy, $orderDirection);
            $bundleCategories = $bundleCategoriesQuery->paginate($perPage, ['*'], 'page', $page);

            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            $index = $start + 1; // Start index from the correct offset
            foreach ($bundleCategories as $bundleCategory) {
                $rowData = [
                    'index' => $index++,
                    'id' => $bundleCategory->id,
                    'translations' => [],
                    'active' => $bundleCategory->active,
                    'created_at' => $bundleCategory->created_at,
                ];

                // Add translations for each language
                foreach ($languages as $language) {
                    $translation = $bundleCategory->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $rowData['translations'][$language->code] = [
                        'name' => $translation ? $translation->lang_value : '-',
                        'rtl' => $language->rtl
                    ];
                }

                // Add first translation name for delete modal
                $firstTranslation = $bundleCategory->translations->where('lang_key', 'name')->first();
                $rowData['first_name'] = $firstTranslation ? $firstTranslation->lang_value : '';

                $data[] = $rowData;
            }

            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $bundleCategories
            ];

        } catch (\Exception $e) {
            Log::error('Error in BundleCategoryAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }
}
