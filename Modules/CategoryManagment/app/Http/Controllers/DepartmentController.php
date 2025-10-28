<?php

namespace Modules\CategoryManagment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CategoryManagment\app\Http\Requests\DepartmentRequest;
use Modules\CategoryManagment\app\Services\DepartmentService;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\CategoryManagment\app\Http\Resources\ActivityResource;
use Modules\CategoryManagment\app\Services\ActivityService;

class DepartmentController extends Controller
{

    public function __construct(
        protected DepartmentService $departmentService, 
        protected LanguageService $languageService,
        protected ActivityService $activityService,
    )
    {
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable(Request $request)
    {
        try {
            // Get pagination parameters
            $perPage = $request->get('per_page', $request->get('length', 10));
            $page = $request->get('page', 1);
            
            // Get filter parameters
            $filters = [
                'search' => $request->get('search'),
                'active' => $request->get('active'),
                'created_date_from' => $request->get('created_date_from'),
                'created_date_to' => $request->get('created_date_to'),
            ];
            
            // Debug logging
            \Log::info('Department Datatable Request:', [
                'all_params' => $request->all(),
                'filters' => $filters
            ]);
            
            // Get total and filtered counts
            $totalRecords = $this->departmentService->getDepartmentsQuery([])->count();
            $filteredRecords = $this->departmentService->getDepartmentsQuery($filters)->count();
            
            // Get departments with pagination
            $departmentsQuery = $this->departmentService->getDepartmentsQuery($filters);
            $departments = $departmentsQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Get languages
            $languages = $this->languageService->getAll();
            
            // Format data for DataTables
            $data = [];
            foreach ($departments as $department) {
                $row = [];
                
                // ID column
                $row[] = $department->id;
                
                // Image column
                if ($department->image) {
                    $row[] = '<img src="' . asset('storage/' . $department->image) . '" alt="Department Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';
                } else {
                    $row[] = '<span class="text-muted">-</span>';
                }
                
                // Name columns for each language
                foreach ($languages as $language) {
                    $translation = $department->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $name = $translation ? $translation->lang_value : '-';
                    
                    if ($language->rtl) {
                        $row[] = '<span dir="rtl">' . e($name) . '</span>';
                    } else {
                        $row[] = e($name);
                    }
                }
                
                // Active status column
                $activeStatus = $department->active 
                    ? '<span class="badge badge-success">' . trans('categorymanagment::department.active') . '</span>'
                    : '<span class="badge badge-danger">' . trans('categorymanagment::department.inactive') . '</span>';
                $row[] = $activeStatus;
                
                // Created at column
                $row[] = $department->created_at->format('Y-m-d H:i');
                
                // Actions
                $actionsHtml = '
                    <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                        <li>
                            <a href="' . route('admin.category-management.departments.show', $department->id) . '" 
                            class="view" 
                            title="' . e(trans('common.view')) . '">
                                <i class="uil uil-eye"></i>
                            </a>
                        </li>
                        <li>
                            <a href="' . route('admin.category-management.departments.edit', $department->id) . '" 
                            class="edit" 
                            title="' . e(trans('common.edit')) . '">
                                <i class="uil uil-edit"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" 
                            class="remove delete-department" 
                            title="' . e(trans('common.delete')) . '"
                            data-bs-toggle="modal" 
                            data-bs-target="#modal-delete-department"
                            data-item-id="' . $department->id . '"
                            data-item-name="' . e($department->translations->where("lang_key", "name")->first()->lang_value ?? "") . '"
                            data-url="' . route('admin.category-management.departments.destroy', $department->id) . '">
                                <i class="uil uil-trash-alt"></i>
                            </a>
                        </li>
                    </ul>';

                $row[] = $actionsHtml;
                
                $data[] = $row;
            }
            
            return response()->json([
                'data' => $data,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'current_page' => $departments->currentPage(),
                'last_page' => $departments->lastPage(),
                'per_page' => $departments->perPage(),
                'total' => $departments->total(),
                'from' => $departments->firstItem(),
                'to' => $departments->lastItem()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading departments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $languages = $this->languageService->getAll();
            return view('categorymanagment::department.index', compact('languages'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', trans('categorymanagment::department.error_loading_departments'));
        }
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = $this->languageService->getAll();
            return view('categorymanagment::department.form', compact('languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.departments.index')
                ->with('error', trans('categorymanagment::department.error_loading_form'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentRequest $request)
    {
        $validated = $request->validated();

        \Log::info('Validated Department Data:', $validated);

        try {
            $this->departmentService->createDepartment($validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('categorymanagment::department.department_created'),
                    'redirect' => route('admin.category-management.departments.index')
                ]);
            }

            return redirect()->route('admin.category-management.departments.index')
                ->with('success', trans('categorymanagment::department.department_created'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('categorymanagment::department.error_creating_department')
                ], 500);
            }

            return redirect()->back()
                ->with('error', trans('categorymanagment::department.error_creating_department'))
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $department = $this->departmentService->getDepartmentById($id);
            return view('categorymanagment::department.view', compact('department', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.departments.index')
                ->with('error', trans('categorymanagment::department.department_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $department = $this->departmentService->getDepartmentById($id);
            return view('categorymanagment::department.form', compact('department', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.departments.index')
                ->with('error', trans('categorymanagment::department.department_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->departmentService->updateDepartment($id, $validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('categorymanagment::department.department_updated'),
                    'redirect' => route('admin.category-management.departments.index')
                ]);
            }

            return redirect()->route('admin.category-management.departments.index')
                ->with('success', trans('categorymanagment::department.department_updated'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('categorymanagment::department.error_updating_department')
                ], 500);
            }

            return redirect()->back()
                ->with('error', trans('categorymanagment::department.error_updating_department'))
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->departmentService->deleteDepartment($id);

            return response()->json([
                'success' => true,
                'message' => trans('categorymanagment::department.department_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('categorymanagment::department.error_deleting_department')
            ], 500);
        }
    }
}
