<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Services\ActivityService;
use App\Services\LanguageService;
use App\Traits\Res;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\CategoryManagment\app\Interfaces\ActivityRepositoryInterface;

class ActivityAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected ActivityService $activityService,
        protected ActivityRepositoryInterface $activityRepositoryInterface
        ) {}

    /**
     * Datatable endpoint for server-side processing
     */
    public function getDataTable($data)
    {
        try {
            // Get pagination parameters
            $perPage = $data['per_page'] ?? $data['length'] ?? 10;
            $page = $data['page'] ?? 1;
            
            // Get filter parameters
            $filters = [
                'search' => $data['search'],
                'active' => $data['active'],
                'created_date_from' => $data['created_date_from'],
                'created_date_to' => $data['created_date_to'],
            ];
            
            // Get total and filtered counts
            $totalRecords = $this->activityRepositoryInterface->getActivitiesQuery([])->count();
            $filteredRecords = $this->activityRepositoryInterface->getActivitiesQuery($filters)->count();
            
            // Get activities with pagination
            $activitiesQuery = $this->activityRepositoryInterface->getActivitiesQuery($filters);
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
            
            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $activities
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in ActivityAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }
        
}
