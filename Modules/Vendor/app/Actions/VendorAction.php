<?php

namespace Modules\Vendor\app\Actions;
use App\Models\User;
use App\Services\LanguageService;
use App\Traits\Res;
use Modules\Vendor\app\Interfaces\VendorInterface;

class VendorAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected VendorInterface $vendorInterface
        )
    {
        
    }
   public function getDataTable($data) {
        $draw = $data['draw'];
        $start = $data['start'];
        $length = $data['length'];
        
        // Get search value from custom parameter or DataTables default
        $searchValue = $data['search'];
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }
        
        $orderColumnIndex = $data['orderColumnIndex'];
        $orderDirection = $data['orderDirection'];

        // Get languages
        $languages = $this->languageService->getAll();

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'active' => $data['active'] ?? '',
            'created_date_from' => $data['created_date_from'],
            'created_date_to' => $data['created_date_to']
        ];

        // Get total records before filtering
        $totalRecords = $this->vendorInterface->getQuery()->count();
        $baseQuery = $this->vendorInterface->getQuery($filters);
        // Get filtered count (clone query to avoid mutation)
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();
        $query = $baseQuery;
        
        // Clear existing orders to prevent conflicts with latest() in base query
        $query->reorder();
        
        // Apply sorting
        // Check if sorting by name column (columns 1 to count($languages))
        if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
            // Get the language for this column
            $languageIndex = $orderColumnIndex - 1;
            $selectedLanguage = $languages->values()->get($languageIndex);
            
            // Join with translations table to sort by translated name
            $query->leftJoin('translations as trans_sort', function($join) use ($selectedLanguage) {
                $join->on('vendors.id', '=', 'trans_sort.translatable_id')
                     ->where('trans_sort.translatable_type', '=', 'Modules\\Vendor\\app\\Models\\Vendor')
                     ->where('trans_sort.lang_key', '=', 'name')
                     ->where('trans_sort.lang_id', '=', $selectedLanguage->id);
            })
            ->orderBy('trans_sort.lang_value', $orderDirection)
            ->select('vendors.*'); // Select only vendors columns to avoid conflicts
        } else {
            // Special handling for commission sorting (relationship)
            if ($orderColumnIndex === (count($languages) + 3)) {
                $query->leftJoin('vendor_commission', 'vendors.id', '=', 'vendor_commission.vendor_id')
                      ->orderBy('vendor_commission.commission', $orderDirection)
                      ->select('vendors.*');
            } else {
                // Build column map for non-translation columns
                $orderColumns = [
                    0 => 'id',
                    (count($languages) + 1) => 'id', // email (sortable via user relationship - using id for now)
                    (count($languages) + 2) => 'country_id', // country
                    (count($languages) + 4) => 'active', // active status
                    (count($languages) + 5) => 'created_at',
                ];

                if (isset($orderColumns[$orderColumnIndex])) {
                    $query->orderBy($orderColumns[$orderColumnIndex], $orderDirection);
                }
            }
        }

        // Apply pagination
        $perPage = $data['length'];
        $page = $data['page'];
        $vendors = $query->with(['translations', 'user', 'country', 'commission'])->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $tableData = [];
        foreach ($vendors as $index => $vendor) {
            $row = [];
            
            // Row number with pagination offset
            $row[] = ($vendors->currentPage() - 1) * $vendors->perPage() + $index + 1;

            // Vendor name for each language
            foreach ($languages as $language) {
                $name = $vendor->getTranslation('name', $language->code) ?? '-';
                $row[] = '<div class="userDatatable-content" ' . ($language->rtl ? 'dir="rtl"' : '') . '>
                            <strong>' . e($name) . '</strong>
                          </div>';
            }

            // Email
            $email = $vendor->user->email ?? '-';
            $row[] = '<div class="userDatatable-content">' . e($email) . '</div>';

            // Country
            $countryName = $vendor->country ? $vendor->country->getTranslation('name', app()->getLocale()) : '-';
            $row[] = '<div class="userDatatable-content">' . e($countryName) . '</div>';

            // Commission
            $commission = ($vendor->commission) ? $vendor->commission->commission : 0;
            $row[] = '<div class="userDatatable-content">
                        <span class="badge badge-info">' . e($commission) . '%</span>
                      </div>';

            // Active Status
            $activeHtml = '<div class="userDatatable-content">';
            if ($vendor->active) {
                $activeHtml .= '<span class="badge badge-success">' . trans('vendor::vendor.active') . '</span>';
            } else {
                $activeHtml .= '<span class="badge badge-danger">' . trans('vendor::vendor.inactive') . '</span>';
            }
            $activeHtml .= '</div>';
            $row[] = $activeHtml;

            // Created at
            $row[] = '<div class="userDatatable-content">' . e($vendor->created_at->format('Y-m-d H:i')) . '</div>';

            // Actions
            $actionsHtml = '<ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                                <li>
                                    <a href="' . route('admin.vendors.show', $vendor->id) . '" class="view" title="' . e(trans('common.view')) . '">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="' . route('admin.vendors.edit', $vendor->id) . '" class="edit" title="' . e(trans('common.edit')) . '">
                                        <i class="uil uil-edit"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" 
                                       class="remove" 
                                       title="' . e(trans('common.delete')) . '"
                                       data-bs-toggle="modal" 
                                       data-bs-target="#modal-delete-vendor"
                                       data-item-id="' . $vendor->id . '"
                                       data-item-name="' . e($name) . '">
                                        <i class="uil uil-trash-alt"></i>
                                    </a>
                                </li>
                            </ul>';
            $row[] = $actionsHtml;

            $tableData[] = $row;
        }


        return [
            'dataPaginated' => $vendors,
            'data' => $tableData,
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords,
        ];
    }
}
