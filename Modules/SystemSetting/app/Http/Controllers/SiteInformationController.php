<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Http\Requests\SiteInformationRequest;
use Modules\SystemSetting\app\Services\SiteInformationService;

class SiteInformationController extends Controller
{
    protected $siteInformationService;

    public function __construct(SiteInformationService $siteInformationService)
    {
        $this->siteInformationService = $siteInformationService;
        
        $this->middleware('can:site-information.index')->only(['index', 'update']);
    }

    public function index()
    {
        $languages = Language::all();
        $siteInformation = $this->siteInformationService->getSiteInformation();
        return view('systemsetting::site-information.form', compact('siteInformation', 'languages'));
    }

    public function update(SiteInformationRequest $request)
    {
        try {
            $data = $request->validated();
            $siteInformation = $this->siteInformationService->updateSiteInformation($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::site-information.updated_successfully'),
                    'data' => $siteInformation
                ]);
            }

            return redirect()
                ->route('admin.system-settings.site-information.index')
                ->with('success', __('systemsetting::site-information.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::site-information.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::site-information.error_updating') . ': ' . $e->getMessage());
        }
    }
}
