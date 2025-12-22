<?php

namespace App\Http\Controllers\UsersVendorsManagement;

use App\Actions\UserVendorRoleAction;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Http\Requests\UsersVendorsManagement\StoreUserVendorRoleRequest;
use App\Http\Requests\UsersVendorsManagement\UpdateUserVendorRoleRequest;
use App\Services\LanguageService;
use App\Services\UserVendorRoleService;
use App\Traits\Res;
use Illuminate\Http\Request;

class UserVendorRoleController extends Controller
{
    use Res;

    public function __construct(
        protected UserVendorRoleService $roleService,
        protected UserVendorRoleAction $roleAction,
        protected LanguageService $languageService)
    {
        $this->middleware('can:users-vendors-roles.index')->only(['index', 'datatable']);
        $this->middleware('can:users-vendors-roles.show')->only(['show']);
        $this->middleware('can:users-vendors-roles.create')->only(['create', 'store']);
        $this->middleware('can:users-vendors-roles.edit')->only(['edit', 'update']);
        $this->middleware('can:users-vendors-roles.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        $languages = $this->languageService->getAll();
        $data = [
            'languages' => $languages,
            'title' => trans('Users Vendors Roles Management'),
        ];
        return view('pages.users_vendors_management.roles.index', $data);
    }

    /**
     * Get roles data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'orderColumnIndex' => $request->get('order')[0]['column'] ?? 0,
            'orderDirection' => $request->get('order')[0]['dir'] ?? 'desc',
            'search' => $request->get('search'),
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
            'type' => 'users_vendors',
        ];

        $response = $this->roleAction->getDataTable($data);
        return response()->json([
            'data' => $response['data'],
            'recordsTotal' => $response['totalRecords'],
            'recordsFiltered' => $response['filteredRecords'],
            'current_page' => $response['dataPaginated']->currentPage(),
            'last_page' => $response['dataPaginated']->lastPage(),
            'per_page' => $response['dataPaginated']->perPage(),
            'total' => $response['dataPaginated']->total(),
            'from' => $response['dataPaginated']->firstItem(),
            'to' => $response['dataPaginated']->lastItem()
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $groupedPermissions = $this->roleService->getGroupedPermissions('users_vendors');
        $data = [
            'languages' => $languages,
            'groupedPermissions' => $groupedPermissions,
            'title' => __('roles.create_role'),
            'type' => 'users_vendors',
        ];
        return view('pages.users_vendors_management.roles.form', $data);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreUserVendorRoleRequest $request)
    {
        $this->roleService->createRole($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role created successfully'),
                'redirect' => route('admin.users-vendors-management.roles.index')
            ]);
        }

        return redirect()->route('admin.users-vendors-management.roles.index')
                        ->with('success', __('Role created successfully'));
    }

    /**
     * Display the specified role.
     */
    public function show($lang, $countryCode, Role $role)
    {
        $languages = $this->languageService->getAll();
        $role = $this->roleService->getRoleById($role->id);
        $data = [
            'role' => $role,
            'languages' => $languages,
            'title' => __('roles.show_role'),
        ];
        return view('pages.users_vendors_management.roles.show', $data);
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($lang, $countryCode, Role $role)
    {
        if ($role->is_system_protected) {
            return redirect()->route('admin.users-vendors-management.roles.index')
                ->with('error', __('This role is system protected and cannot be edited.'));
        }
        $role = $this->roleService->getRoleById($role->id);
        $languages = $this->languageService->getAll();
        $groupedPermissions = $this->roleService->getGroupedPermissions('users_vendors');
        $data = [
            'role' => $role,
            'languages' => $languages,
            'groupedPermissions' => $groupedPermissions,
            'title' => __('roles.edit_role'),
            'type' => 'users_vendors',
        ];
        return view('pages.users_vendors_management.roles.form', $data);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateUserVendorRoleRequest $request, $lang, $countryCode, Role $role)
    {
        if ($role->is_system_protected) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => __('This role is system protected and cannot be edited.')], 403);
            }
            return redirect()->route('admin.users-vendors-management.roles.index')
                ->with('error', __('This role is system protected and cannot be edited.'));
        }
        
        $this->roleService->updateRole($role, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role updated successfully'),
                'redirect' => route('admin.users-vendors-management.roles.index')
            ]);
        }

        return redirect()->route('admin.users-vendors-management.roles.index')
                        ->with('success', __('Role updated successfully'));
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Request $request, $lang, $countryCode, Role $role)
    {
        if ($role->is_system_protected) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('This is a system role and cannot be deleted. You can only edit it.')
                ], 403);
            }

            return redirect()->route('admin.users-vendors-management.roles.index')
                            ->with('error', __('This is a system role and cannot be deleted. You can only edit it.'));
        }

        $this->roleService->deleteRole($role);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role deleted successfully')
            ]);
        }

        return redirect()->route('admin.users-vendors-management.roles.index')
                        ->with('success', __('Role deleted successfully'));
    }
}
