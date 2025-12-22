<?php

namespace App\Services;

use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;

class UserVendorRoleService
{
    public function __construct(
        public RoleRepositoryInterface $roleRepositoryInterface,
        protected LanguageService $languageService
        )
    {
    }

    /**
     * Get all roles with permissions
     */
    public function getAllRoles($filter = [], $per_page = 10)
    {
        return $this->roleRepositoryInterface->getAll($filter, $per_page);
    }

    public function getRolesQuery($filter = [])
    {
        return $this->roleRepositoryInterface->getRolesQuery($filter);
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

        $totalRecords = $this->getRolesQuery()->count();
        $baseQuery = $this->getRolesQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();
        $query = $baseQuery;

        $query->reorder();

        if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
            $languageIndex = $orderColumnIndex - 1;
            $selectedLanguage = $languages->values()->get($languageIndex);

            $query->leftJoin('translations as trans_sort', function($join) use ($selectedLanguage) {
                $join->on('user_vendor_roles.id', '=', 'trans_sort.translatable_id')
                     ->where('trans_sort.translatable_type', '=', 'App\\Models\\UserVendorRole')
                     ->where('trans_sort.lang_key', '=', 'name')
                     ->where('trans_sort.lang_id', '=', $selectedLanguage->id);
            })
            ->orderBy('trans_sort.lang_value', $orderDirection)
            ->select('user_vendor_roles.*');
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
            $row = [];

            $row[] = ($roles->currentPage() - 1) * $roles->perPage() + $index + 1;

            foreach ($languages as $language) {
                $name = $role->getTranslation('name', $language->code) ?? '-';
                $row[] = '<div class="userDatatable-content" ' . ($language->rtl ? 'dir="rtl"' : '') . '>
                            <strong>' . e($name) . '</strong>
                          </div>';
            }

            $permissionsCount = $role->permessions->count();
            $permissionsHtml = '<div class="userDatatable-content">
                                    <span class="badge badge-primary" style="border-radius: 6px; padding: 6px 12px;">
                                        <i class="uil uil-shield-check me-1"></i>' . $permissionsCount . ' ' . trans('roles.permissions') . '
                                    </span>
                                </div>';
            $row[] = $permissionsHtml;

            $row[] = '<div class="userDatatable-content">' . e($role->created_at) . '</div>';

            $actionsHtml = '<ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                                <li>
                                    <a href="' . route('admin.users-vendors-management.roles.show', $role->id) . '" class="view" title="' . e(trans('common.view')) . '">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="' . route('admin.users-vendors-management.roles.edit', $role->id) . '" class="edit" title="' . e(trans('common.edit')) . '">
                                        <i class="uil uil-edit"></i>
                                    </a>
                                </li>';
            
            if (!$role->is_system_protected && $role->is_system_protected != 1) {
                $actionsHtml .= '<li>
                                    <a href="javascript:void(0);"
                                       class="remove"
                                       title="' . e(trans('common.delete')) . '"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modal-delete-role"
                                       data-item-id="' . $role->id . '"
                                       data-item-name="' . e($role->name) . '">
                                        <i class="uil uil-trash-alt"></i>
                                    </a>
                                </li>';
            }

            $actionsHtml .= '</ul>';
            $row[] = $actionsHtml;

            $data[] = $row;
        }

        return [
            'dataPaginated' => $roles,
            'data' => $data,
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords,
        ];
    }

    /**
     * Get a single role by ID
     */
    public function getRoleById(int $id): ?Role
    {
        return $this->roleRepositoryInterface->findById($id);
    }

    /**
     * Get grouped permissions
     */
    public function getGroupedPermissions($type = null): array
    {
        return $this->roleRepositoryInterface->getGroupedPermissions($type);
    }

    /**
     * Create a new role
     */
    public function createRole(array $data)
    {
        return $this->roleRepositoryInterface->create($data);
    }

    /**
     * Update an existing role
     */
    public function updateRole(Role $role, array $data)
    {
        $this->roleRepositoryInterface->update($role, $data);
    }

    /**
     * Delete a role
     */
    public function deleteRole(Role $role)
    {
        $this->roleRepositoryInterface->delete($role);
    }

    /**
     * Assign permissions to a role
     */
    public function assignPermissions(Role $role, array $permissionIds): void
    {
        $this->roleRepositoryInterface->syncPermissions($role, $permissionIds);
    }
}
