<?php

namespace App\Actions;

use App\Models\UserType;
use App\Services\UserVendorService;
use App\Services\LanguageService;
use Illuminate\Http\Request;

class UserVendorAction
{
    public function __construct(
        protected UserVendorService $userVendorService,
        protected LanguageService $languageService
    ) {
    }

    /**
     * Get users vendors data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);

        $searchValue = $request->get('search');
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }

        $orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
        $orderDirection = $request->get('order')[0]['dir'] ?? 'desc';

        $active = $request->get('active');
        $roleId = $request->get('role_id');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        $filters = [
            'search' => $searchValue,
            'active' => $active,
            'role_id' => $roleId,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        $languages = $this->languageService->getAll();

        $totalRecords = $this->userVendorService->getUserVendorsQuery([])->count();

        $baseQuery = $this->userVendorService->getUserVendorsQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();

        $orderBy = $this->determineSorting($request, $languages, $orderColumnIndex);

        $query = $this->userVendorService->getUserVendorsQuery($filters, $orderBy, $orderDirection);

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $usersVendors = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $this->formatDataForDataTables($usersVendors, $languages);

        return response()->json([
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'current_page' => $usersVendors->currentPage(),
            'last_page' => $usersVendors->lastPage(),
            'per_page' => $usersVendors->perPage(),
            'total' => $usersVendors->total(),
            'from' => $usersVendors->firstItem(),
            'to' => $usersVendors->lastItem()
        ]);
    }

    /**
     * Determine sorting parameters based on request
     */
    protected function determineSorting(Request $request, $languages, int $orderColumnIndex)
    {
        $orderBy = null;
        $sortBy = $request->get('sort_by');

        if ($sortBy) {
            if (strpos($sortBy, 'name_') === 0) {
                $languageId = str_replace('name_', '', $sortBy);
                $orderBy = ['lang_id' => $languageId];
            } else {
                $orderBy = $sortBy;
            }
        } else {
            $orderColumns = [
                0 => 'id',
                2 => 'email',
                4 => 'active',
                5 => 'block',
                6 => 'created_at',
            ];

            if (isset($orderColumns[$orderColumnIndex])) {
                $orderBy = $orderColumns[$orderColumnIndex];
            }
        }

        return $orderBy;
    }

    /**
     * Format data for DataTables response
     */
    protected function formatDataForDataTables($usersVendors, $languages)
    {
        $data = [];

        foreach ($usersVendors as $userVendor) {
            $row = [];

            $row['id'] = $userVendor->id;

            $row['names'] = [];
            foreach ($languages as $language) {
                $translation = $userVendor->translations()
                    ->where('lang_id', $language->id)
                    ->where('lang_key', 'name')
                    ->first();

                $row['names'][$language->id] = [
                    'value' => $translation ? $translation->lang_value : '-',
                    'rtl' => $language->rtl,
                    'code' => $language->code
                ];
            }

            $row['email'] = $userVendor->email;

            if ($userVendor->roles->isNotEmpty()) {
                $rolesHtml = '';
                foreach ($userVendor->roles as $role) {
                    $roleName = $role->getTranslation('name', app()->getLocale());
                    $rolesHtml .= '<span class="badge badge-info badge-round badge-sm me-1 mb-1">' . e($roleName) . '</span>';
                }
                $row['role'] = '<div class="userDatatable-content">' . $rolesHtml . '</div>';
            } else {
                $row['role'] = '<span class="color-gray">-</span>';
            }

            $row['active'] = $userVendor->active ?? true;
            $row['block'] = $userVendor->block ?? false;
            $row['image'] = $userVendor->image;
            $row['created_at'] = $userVendor->created_at ? $userVendor->created_at : '-';

            $nameTranslation = $userVendor->translations()->where('lang_key', 'name')->first();
            $row['display_name'] = $nameTranslation && $nameTranslation->lang_value
                ? $nameTranslation->lang_value
                : 'User Vendor';

            $data[] = $row;
        }

        return $data;
    }
}
