<?php

namespace App\Http\Controllers\UsersVendorsManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsersVendorsManagement\UserVendorRequest;
use App\Services\UserVendorService;
use App\Services\LanguageService;
use App\Services\UserVendorRoleService;
use App\Actions\UserVendorAction;
use Illuminate\Http\Request;

class UserVendorController extends Controller
{
    public function __construct(
        protected UserVendorService $userVendorService,
        protected LanguageService $languageService,
        protected UserVendorRoleService $roleService,
        protected UserVendorAction $userVendorAction
    ) {
        $this->middleware('can:users-vendors.index')->only(['index', 'datatable']);
        $this->middleware('can:users-vendors.create')->only(['create', 'store']);
        $this->middleware('can:users-vendors.edit')->only(['edit', 'update']);
        $this->middleware('can:users-vendors.delete')->only(['destroy']);
        $this->middleware('can:users-vendors.show')->only(['show']);
        $this->middleware('can:users-vendors.change-status')->only(['changeStatus']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $lang, $countryCode)
    {
        $languages = $this->languageService->getAll();
        return view('pages.users_vendors_management.users_vendors.index', compact('languages'));
    }

    /**
     * Get users vendors data for DataTables AJAX
     */
    public function datatable(Request $request, $lang, $countryCode)
    {
        return $this->userVendorAction->datatable($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($lang, $countryCode)
    {
        $languages = $this->languageService->getAll();
        $roles = $this->roleService->getAllRoles(['exclude_system' => true], 0);
        return view('pages.users_vendors_management.users_vendors.form', compact('languages', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserVendorRequest $request, $lang, $countryCode)
    {
        $validated = $request->validated();

        try {
            $this->userVendorService->createUserVendor($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('user_vendor.user_vendor_created_successfully'),
                    'redirect' => route('admin.users-vendors-management.users-vendors.index')
                ]);
            }

            return redirect()->route('admin.users-vendors-management.users-vendors.index')
                ->with('success', __('user_vendor.user_vendor_created_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('user_vendor.error_creating_user_vendor') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('user_vendor.error_creating_user_vendor') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $userVendor = $this->userVendorService->getUserVendorById((int) $id);
            return view('pages.users_vendors_management.users_vendors.view', compact('userVendor', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.users-vendors-management.users-vendors.index')
                ->with('error', __('user_vendor.user_vendor_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $userVendor = $this->userVendorService->getUserVendorById((int) $id);
            $roles = $this->roleService->getAllRoles(['exclude_system' => true], 0);
            return view('pages.users_vendors_management.users_vendors.form', compact('userVendor', 'languages', 'roles'));
        } catch (\Exception $e) {
            return redirect()->route('admin.users-vendors-management.users-vendors.index')
                ->with('error', __('user_vendor.user_vendor_not_found'));
        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(UserVendorRequest $request, $lang, $countryCode, string $id)
    {
        $validated = $request->validated();

        try {
            $this->userVendorService->updateUserVendor((int) $id, $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('user_vendor.user_vendor_updated_successfully'),
                    'redirect' => route('admin.users-vendors-management.users-vendors.index')
                ]);
            }

            return redirect()->route('admin.users-vendors-management.users-vendors.index')
                ->with('success', __('user_vendor.user_vendor_updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('user_vendor.error_updating_user_vendor') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('user_vendor.error_updating_user_vendor') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $lang, $countryCode, string $id)
    {
        try {
            $this->userVendorService->deleteUserVendor((int) $id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('user_vendor.user_vendor_deleted_successfully'),
                    'redirect' => route('admin.users-vendors-management.users-vendors.index')
                ]);
            }

            return redirect()->route('admin.users-vendors-management.users-vendors.index')
                ->with('success', __('user_vendor.user_vendor_deleted_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('user_vendor.error_deleting_user_vendor') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.users-vendors-management.users-vendors.index')
                ->with('error', __('user_vendor.error_deleting_user_vendor') . ': ' . $e->getMessage());
        }
    }

    /**
     * Change user vendor status.
     */
    public function changeStatus(Request $request, $lang, $countryCode, string $id)
    {
        try {
            $this->userVendorService->changeStatus((int) $id, $request->status, $request->type);
            return response()->json([
                'success' => true,
                'message' => __('user_vendor.status_changed_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
