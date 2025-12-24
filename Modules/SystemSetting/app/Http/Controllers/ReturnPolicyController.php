<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Http\Requests\ReturnPolicyRequest;
use Modules\SystemSetting\app\Services\ReturnPolicyService;

class ReturnPolicyController extends Controller
{
    protected $returnPolicyService;

    public function __construct(ReturnPolicyService $returnPolicyService)
    {
        $this->returnPolicyService = $returnPolicyService;
        
        $this->middleware('can:return-policy.index')->only(['index', 'update']);
    }

    public function index()
    {
        $languages = Language::all();
        $returnPolicy = $this->returnPolicyService->getReturnPolicy();
        // Load translations relationship to ensure they're available in the view
        if ($returnPolicy) {
            $returnPolicy->load('translations');
        }
        return view('systemsetting::return-policy.form', compact('returnPolicy', 'languages'));
    }

    public function update(ReturnPolicyRequest $request)
    {
        try {
            $data = $request->validated();

            // Verify data is being received
            if (empty($data['description'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::return-policy.error_updating') . ': No description data received'
                ], 400);
            }

            $returnPolicy = $this->returnPolicyService->updateReturnPolicy($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::return-policy.updated_successfully'),
                    'data' => $returnPolicy
                ]);
            }

            return redirect()
                ->route('admin.system-settings.return-policy.index')
                ->with('success', __('systemsetting::return-policy.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::return-policy.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::return-policy.error_updating') . ': ' . $e->getMessage());
        }
    }
}
