<?php

namespace Modules\CategoryManagment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CategoryManagment\app\Http\Requests\CategoryRequest;
use Modules\CategoryManagment\app\Services\CategoryService;
use Modules\CategoryManagment\app\Services\DepartmentService;
use Modules\CategoryManagment\app\Services\ActivityService;
use Modules\CategoryManagment\app\Http\Resources\ActivityResource;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\Http\Resources\DepartmentResource;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $departmentService;
    protected $activityService;
    protected $languageService;

    public function __construct(CategoryService $categoryService, DepartmentService $departmentService, ActivityService $activityService, LanguageService $languageService)
    {
        $this->categoryService = $categoryService;
        $this->departmentService = $departmentService;
        $this->activityService = $activityService;
        $this->languageService = $languageService;
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
                'department_id' => $request->get('department_id'),
                'active' => $request->get('active'),
                'created_date_from' => $request->get('created_date_from'),
                'created_date_to' => $request->get('created_date_to'),
            ];
            
            // Debug logging
            \Log::info('Category Datatable Request:', [
                'all_params' => $request->all(),
                'filters' => $filters
            ]);
            
            // Get total and filtered counts
            $totalRecords = $this->categoryService->getCategoriesQuery([])->count();
            $filteredRecords = $this->categoryService->getCategoriesQuery($filters)->count();
            
            // Get categories with pagination
            $categoriesQuery = $this->categoryService->getCategoriesQuery($filters);
            $categories = $categoriesQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Get languages
            $languages = $this->languageService->getAll();
            
            // Format data for DataTables
            $data = [];
            foreach ($categories as $category) {
                $row = [];
                
                // ID column
                $row[] = $category->id;
                
                // Name columns for each language
                foreach ($languages as $language) {
                    $translation = $category->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $name = $translation ? $translation->lang_value : '-';
                    
                    if ($language->rtl) {
                        $row[] = '<span dir="rtl">' . e($name) . '</span>';
                    } else {
                        $row[] = e($name);
                    }
                }
                
                // Department column (changed from departments to department - belongsTo relationship)
                if ($category->department) {
                    $deptName = $category->department->getTranslation('name', app()->getLocale());
                    $row[] = '<span class="badge badge-info">' . e($deptName) . '</span>';
                } else {
                    $row[] = '-';
                }
                
                // Active status column
                $activeStatus = $category->active 
                    ? '<span class="badge badge-success">' . trans('categorymanagment::category.active') . '</span>'
                    : '<span class="badge badge-danger">' . trans('categorymanagment::category.inactive') . '</span>';
                $row[] = $activeStatus;
                
                // Created at column
                $row[] = $category->created_at->format('Y-m-d H:i');
                
                // Actions
                $actionsHtml = '
                    <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                        <li>
                            <a href="' . route('admin.category-management.categories.show', $category->id) . '" 
                            class="view" 
                            title="' . e(trans('common.view')) . '">
                                <i class="uil uil-eye"></i>
                            </a>
                        </li>
                        <li>
                            <a href="' . route('admin.category-management.categories.edit', $category->id) . '" 
                            class="edit" 
                            title="' . e(trans('common.edit')) . '">
                                <i class="uil uil-edit"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" 
                            class="remove delete-category" 
                            title="' . e(trans('common.delete')) . '"
                            data-bs-toggle="modal" 
                            data-bs-target="#modal-delete-category"
                            data-item-id="' . $category->id . '"
                            data-item-name="' . e($category->translations->where("lang_key", "name")->first()->lang_value ?? "") . '"
                            data-url="' . route('admin.category-management.categories.destroy', $category->id) . '">
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
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading categories: ' . $e->getMessage()
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
            return view('categorymanagment::category.index', compact('languages'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', trans('categorymanagment::category.error_loading_categories'));
        }
    }

    /**
     * AJAX endpoint for searching activities (Select2)
     */
    public function searchActivities(Request $request)
    {
        try {
            $data = $this->activityService->searchForSelect2(
                $request->get('q', ''),
                $request->get('page', 1)
            );
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false]
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $departments = $this->departmentService->getActiveDepartments();
            $departments = DepartmentResource::collection($departments)->resolve();
            $languages = $this->languageService->getAll();
            return view('categorymanagment::category.form', compact('languages', 'departments'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.categories.index')
                ->with('error', trans('categorymanagment::category.error_loading_form'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->categoryService->createCategory($validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('categorymanagment::category.category_created'),
                    'redirect' => route('admin.category-management.categories.index')
                ]);
            }

            return redirect()->route('admin.category-management.categories.index')
                ->with('success', trans('categorymanagment::category.category_created'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('categorymanagment::category.error_creating_category')
                ], 500);
            }

            return redirect()->back()
                ->with('error', trans('categorymanagment::category.error_creating_category'))
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
            $category = $this->categoryService->getCategoryById($id);
            return view('categorymanagment::category.view', compact('category', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.categories.index')
                ->with('error', trans('categorymanagment::category.category_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $departments = $this->departmentService->getActiveDepartments();
            $departments = DepartmentResource::collection($departments)->resolve();
            $category = $this->categoryService->getCategoryById($id);
            return view('categorymanagment::category.form', compact('category', 'languages', 'departments'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.categories.index')
                ->with('error', trans('categorymanagment::category.category_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->categoryService->updateCategory($id, $validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('categorymanagment::category.category_updated'),
                    'redirect' => route('admin.category-management.categories.index')
                ]);
            }

            return redirect()->route('admin.category-management.categories.index')
                ->with('success', trans('categorymanagment::category.category_updated'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating category')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Error updating category'))
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->categoryService->deleteCategory($id);

            return response()->json([
                'success' => true,
                'message' => trans('categorymanagment::category.category_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('categorymanagment::category.error_deleting_category')
            ], 500);
        }
    }
}
