<?php

namespace Modules\CategoryManagment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Modules\CategoryManagment\app\Services\ActivityService;
use Modules\CategoryManagment\app\Http\Requests\ActivityRequest;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    public function __construct(protected ActivityService $activityService, 
    protected LanguageService $languageService)
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
            \Log::info('Activity Datatable Request:', [
                'all_params' => $request->all(),
                'filters' => $filters
            ]);
            
            // Get total and filtered counts
            $totalRecords = $this->activityService->getActivitiesQuery([])->count();
            $filteredRecords = $this->activityService->getActivitiesQuery($filters)->count();
            
            // Get activities with pagination
            $activitiesQuery = $this->activityService->getActivitiesQuery($filters);
            $activities = $activitiesQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Get languages
            $languages = $this->languageService->getAll();
            
            // Format data for DataTables
            $data = [];
            foreach ($activities as $activity) {
                $row = [];
                
                // ID column
                $row[] = $activity->id;
                
                // Name columns for each language
                foreach ($languages as $language) {
                    $translation = $activity->translations->where('lang_id', $language->id)
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
                $activeStatus = $activity->active 
                    ? '<span class="badge badge-success">' . __('activity.active') . '</span>'
                    : '<span class="badge badge-danger">' . __('activity.inactive') . '</span>';
                $row[] = $activeStatus;
                
                // Created at column
                $row[] = $activity->created_at->format('Y-m-d H:i');
                
                // Actions
                $actionsHtml = '
                    <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                        <li>
                            <a href="' . route('admin.category-management.activities.show', $activity->id) . '" 
                            class="view" 
                            title="' . e(trans('common.view')) . '">
                                <i class="uil uil-eye"></i>
                            </a>
                        </li>
                        <li>
                            <a href="' . route('admin.category-management.activities.edit', $activity->id) . '" 
                            class="edit" 
                            title="' . e(trans('common.edit')) . '">
                                <i class="uil uil-edit"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" 
                            class="remove delete-activity" 
                            title="' . e(trans('common.delete')) . '"
                            data-bs-toggle="modal" 
                            data-bs-target="#modal-delete-activity"
                            data-item-id="' . $activity->id . '"
                            data-item-name="' . e($activity->translations->where("lang_key", "name")->first()->lang_value ?? "") . '"
                            data-url="' . route('admin.category-management.activities.destroy', $activity->id) . '">
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
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
                'from' => $activities->firstItem(),
                'to' => $activities->lastItem()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading activities: ' . $e->getMessage()
            ], 500);
        }
    }


    public function activitySearch(Request $request) {
        return $this->activityService->searchForSelect2($request->q, $request->page);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();
        return view('categorymanagment::activity.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        return view('categorymanagment::activity.form', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ActivityRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->activityService->createActivity($validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Activity created successfully'),
                    'redirect' => route('admin.category-management.activities.index')
                ]);
            }
            
            return redirect()->route('admin.category-management.activities.index')
                ->with('success', __('Activity created successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating activity: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating activity: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $activity = $this->activityService->getActivityById($id);
            $languages = $this->languageService->getAll();
            return view('categorymanagment::activity.view', compact('activity', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.activities.index')
                ->with('error', __('Activity not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $activity = $this->activityService->getActivityById($id);
            return view('categorymanagment::activity.form', compact('activity', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.activities.index')
                ->with('error', __('Activity not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActivityRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->activityService->updateActivity($id, $validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Activity updated successfully'),
                    'redirect' => route('admin.category-management.activities.index')
                ]);
            }
            
            return redirect()->route('admin.category-management.activities.index')
                ->with('success', __('Activity updated successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating activity: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating activity: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->activityService->deleteActivity($id);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Activity deleted successfully'),
                    'redirect' => route('admin.category-management.activities.index')
                ]);
            }
            
            return redirect()->route('admin.category-management.activities.index')
                ->with('success', __('Activity deleted successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error deleting activity: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->route('admin.category-management.activities.index')
                ->with('error', __('Error deleting activity: ') . $e->getMessage());
        }
    }
}
