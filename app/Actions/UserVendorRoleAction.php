<?php

namespace App\Actions;

use App\Interfaces\RoleRepositoryInterface;
use App\Services\LanguageService;
use App\Traits\Res;

class UserVendorRoleAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected RoleRepositoryInterface $roleRepositoryInterface
        )
    {
    }

   public function getDataTable($data) {
        $draw = $data['draw'];
        $start = $data['start'];
        $length = $data['length'];

        $searchValue = $data['search'];
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }

        $orderColumnIndex = $data['orderColumnIndex'];
        $orderDirection = $data['orderDirection'];

        $languages = $this->languageService->getAll();

        $filters = [
            'search' => $searchValue,
            'created_date_from' => $data['created_date_from'],
            'created_date_to' => $data['created_date_to']
        ];

        $totalRecords = $this->roleRepositoryInterface->getRolesQuery()->count();
        $baseQuery = $this->roleRepositoryInterface->getRolesQuery($filters);
        
        if (isset($data['type'])) {
            $baseQuery->where('type', $data['type']);
        }

        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();
        $query = $baseQuery;

        $query->reorder();

        if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
            $languageIndex = $orderColumnIndex - 1;
            $selectedLanguage = $languages->values()->get($languageIndex);

            $query->leftJoin('translations as trans_sort', function($join) use ($selectedLanguage) {
                $join->on('roles.id', '=', 'trans_sort.translatable_id')
                     ->where('trans_sort.translatable_type', '=', 'App\\Models\\Role')
                     ->where('trans_sort.lang_key', '=', 'name')
                     ->where('trans_sort.lang_id', '=', $selectedLanguage->id);
            })
            ->orderBy('trans_sort.lang_value', $orderDirection)
            ->select('roles.*');
        } else {
            $orderColumns = [
                0 => 'id',
                (count($languages) + 1) => 'id',
                (count($languages) + 2) => 'created_at',
            ];

            if (isset($orderColumns[$orderColumnIndex])) {
                $query->orderBy($orderColumns[$orderColumnIndex], $orderDirection);
            }
        }

        $perPage = $data['length'];
        $page = $data['page'];

        $roles = $query->with(['permessions', 'translations'])->paginate($perPage, ['*'], 'page', $page);

        $data = [];
        foreach ($roles as $index => $role) {
            $rowData = [
                'row_number' => ($roles->currentPage() - 1) * $roles->perPage() + $index + 1,
                'id' => $role->id,
                'type' => $role->type,
                'is_system_protected' => $role->is_system_protected,
                'translations' => [],
                'permissions_count' => $role->permessions->count(),
                'created_at' => $role->created_at,
                'name' => $role->name,
            ];

            foreach ($languages as $language) {
                $name = $role->getTranslation('name', $language->code) ?? '-';
                $rowData['translations'][$language->code] = [
                    'name' => $name,
                    'rtl' => $language->rtl
                ];
            }

            $data[] = $rowData;
        }

        return [
            'dataPaginated' => $roles,
            'data' => $data,
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords,
        ];
    }
}
