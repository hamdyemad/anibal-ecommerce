<?php

namespace Modules\CategoryManagment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CategoryManagment\app\Http\Requests\SubCategoryRequest;
use Modules\CategoryManagment\app\Services\SubCategoryService;
use Modules\CategoryManagment\app\Services\CategoryService;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubCategoryController extends Controller
{
    protected $subCategoryService;
    protected $categoryService;
    protected $languageService;

    public function __construct(SubCategoryService $subCategoryService, CategoryService $categoryService, LanguageService $languageService)
    {
        $this->subCategoryService = $subCategoryService;
        $this->categoryService = $categoryService;
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
                'category_id' => $request->get('category_id'),
                'active' => $request->get('active'),
                'created_date_from' => $request->get('created_date_from'),
                'created_date_to' => $request->get('created_date_to'),
            ];

            // Debug logging
            Log::info('SubCategory Datatable Request:', [
                'all_params' => $request->all(),
                'filters' => $filters,
                'per_page' => $perPage,
                'page' => $page
            ]);
                
            // Get total and filtered counts
            $totalRecords = $this->subCategoryService->getSubCategoriesQuery([])->count();
            $filteredRecords = $this->subCategoryService->getSubCategoriesQuery($filters)->count();
            
            // Get sub-categories with pagination
            $subCategoriesQuery = $this->subCategoryService->getSubCategoriesQuery($filters);
            $subCategories = $subCategoriesQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Get languages
            $languages = $this->languageService->getAll();
            
            // Format data for DataTables
            $data = [];
            foreach ($subCategories as $subCategory) {
                $row = [];
                
                // ID column
                $row[] = $subCategory->id;
                
                // Name columns for each language
                foreach ($languages as $language) {
                    $translation = $subCategory->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $name = $translation ? $translation->lang_value : '-';
                    
                    if ($language->rtl) {
                        $row[] = '<span dir="rtl">' . e($name) . '</span>';
                    } else {
                        $row[] = e($name);
                    }
                }
                
                // Category column
                if ($subCategory->category) {
                    $firstLang = $languages->first();
                    $catTrans = $subCategory->category->translations->where('lang_id', $firstLang->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $row[] = '<span class="badge badge-round badge-primary badge-lg" data-category-id="' . $subCategory->category->id . '">' . e($catTrans->lang_value ?? '') . '</span>';
                } else {
                    $row[] = '-';
                }
                
                // Active status column
                $activeStatus = $subCategory->active 
                    ? '<span class="badge badge-round badge-success badge-lg">' . __('subcategory.active') . '</span>'
                    : '<span class="badge badge-round badge-danger badge-lg">' . __('subcategory.inactive') . '</span>';
                $row[] = $activeStatus;
                
                // Created at column
                $row[] = $subCategory->created_at->format('Y-m-d H:i');
                
                // Actions
                $actionsHtml = '
                    <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                        <li>
                            <a href="' . route('admin.category-management.subcategories.show', $subCategory->id) . '" 
                            class="view" 
                            title="' . e(trans('common.view')) . '">
                                <i class="uil uil-eye"></i>
                            </a>
                        </li>
                        <li>
                            <a href="' . route('admin.category-management.subcategories.edit', $subCategory->id) . '" 
                            class="edit" 
                            title="' . e(trans('common.edit')) . '">
                                <i class="uil uil-edit"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" 
                            class="remove delete-subcategory" 
                            title="' . e(trans('common.delete')) . '"
                            data-bs-toggle="modal" 
                            data-bs-target="#modal-delete-subcategory"
                            data-item-id="' . $subCategory->id . '"
                            data-item-name="' . e($subCategory->translations->where("lang_key", "name")->first()->lang_value ?? "") . '"
                            data-url="' . route('admin.category-management.subcategories.destroy', $subCategory->id) . '">
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
                'current_page' => $subCategories->currentPage(),
                'last_page' => $subCategories->lastPage(),
                'per_page' => $subCategories->perPage(),
                'total' => $subCategories->total(),
                'from' => $subCategories->firstItem(),
                'to' => $subCategories->lastItem()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading sub-categories: ' . $e->getMessage()
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
            $categories = $this->categoryService->getActiveCategories();
            return view('categorymanagment::subcategory.index', compact('languages', 'categories'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Error loading sub-categories'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = $this->languageService->getAll();
            $categories = $this->categoryService->getActiveCategories();
            return view('categorymanagment::subcategory.form', compact('languages', 'categories'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.subcategories.index')
                ->with('error', __('Error loading form'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubCategoryRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->subCategoryService->createSubCategory($validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Sub-category created successfully'),
                    'redirect' => route('admin.category-management.subcategories.index')
                ]);
            }

            return redirect()->route('admin.category-management.subcategories.index')
                ->with('success', __('Sub-category created successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating sub-category')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Error creating sub-category'))
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $languages = $this->languageService->getAll();
        $subCategory = $this->subCategoryService->getSubCategoryById($id);
        try {
            return view('categorymanagment::subcategory.view', compact('subCategory', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.subcategories.index')
                ->with('error', __('Sub-category not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $categories = $this->categoryService->getActiveCategories();
            $subCategory = $this->subCategoryService->getSubCategoryById($id);
            return view('categorymanagment::subcategory.form', compact('subCategory', 'languages', 'categories'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.subcategories.index')
                ->with('error', __('Sub-category not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubCategoryRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->subCategoryService->updateSubCategory($id, $validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Sub-category updated successfully'),
                    'redirect' => route('admin.category-management.subcategories.index')
                ]);
            }

            return redirect()->route('admin.category-management.subcategories.index')
                ->with('success', __('Sub-category updated successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating sub-category')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Error updating sub-category'))
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->subCategoryService->deleteSubCategory($id);

            return response()->json([
                'success' => true,
                'message' => __('Sub-category deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting sub-category')
            ], 500);
        }
    }
}
