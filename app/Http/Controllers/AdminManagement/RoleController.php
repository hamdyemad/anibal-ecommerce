<?php

namespace App\Http\Controllers\AdminManagement;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\LanguageService;
use App\Traits\Res;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use Res;

    public function __construct(protected RoleService $roleService, protected LanguageService $languageService)
    {
    }

    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $roles = $this->roleService->getAllRoles();
        
        return view('pages.admin_management.roles.index', compact('roles', 'search', 'dateFrom', 'dateTo'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $languages = $this->roleService->getLanguages();
        $groupedPermissions = $this->roleService->getGroupedPermissions();
        
        return view('pages.admin_management.roles.form', compact('groupedPermissions', 'languages'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        // Create the role
        $this->roleService->createRole($request->validated());

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return $this->sendRes(__('Role created successfully'), route('admin.admin-management.roles.index'));
            
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $languages = $this->languageService->getAll();
        $role = $this->roleService->getRoleById($role->id);
        
        return view('pages.admin_management.roles.show', compact('role', 'languages'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $role = $this->roleService->getRoleById($role->id);
        $languages = $this->roleService->getLanguages();
        $groupedPermissions = $this->roleService->getGroupedPermissions();
        return view('pages.admin_management.roles.form', compact('role', 'groupedPermissions', 'languages'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        // Update the role
        $this->roleService->updateRole($role, $request->validated());

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role updated successfully'),
                'redirect' => route('admin.admin-management.roles.index')
            ]);
        }

        return redirect()->route('admin.admin-management.roles.index')
                        ->with('success', __('Role updated successfully'));
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Request $request, Role $role)
    {
        $this->roleService->deleteRole($role);

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role deleted successfully')
            ]);
        }

        return redirect()->route('admin.admin-management.roles.index')
                        ->with('success', __('Role deleted successfully'));
    }
}
