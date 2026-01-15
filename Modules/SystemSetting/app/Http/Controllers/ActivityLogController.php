<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Actions\ActivityLogAction;
use Modules\SystemSetting\app\Services\ActivityLogService;
use App\Services\LanguageService;

class ActivityLogController extends Controller
{
    public function __construct(
        protected ActivityLogService $activityLogService,
        protected LanguageService $languageService,
        protected ActivityLogAction $activityLogAction
    )
    {
        $this->middleware('can:settings.logs.view')->only(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = [
            'title' => __('systemsetting::activity_log.activity_logs_management'),
        ];
        return view('systemsetting::activity-log.index', $data);
    }

    /**
     * Get activity logs data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = $this->activityLogAction->getDatatableData($request);
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $lang, $countryCode, string $id)
    {
        try {
            $activityLog = $this->activityLogService->getActivityLogById($id);
            
            if (!$activityLog) {
                throw new \Exception('Activity log not found');
            }
            
            $data = [
                'activity_log' => $activityLog,
                'title' => __('systemsetting::activity_log.view_activity_log'),
            ];
            
            // Return partial view for AJAX requests (modal)
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return view('systemsetting::activity-log.show-modal', $data);
            }
            
            return view('systemsetting::activity-log.show', $data);
        } catch (\Exception $e) {
            \Log::error('Activity log show error: ' . $e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response('<div class="alert alert-danger"><i class="uil uil-exclamation-triangle me-2"></i>' . $e->getMessage() . '</div>', 200);
            }
            return redirect()->route('admin.system-settings.activity-logs.index')
                ->with('error', $e->getMessage());
        }
    }
}
