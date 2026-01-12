<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Modules\SystemSetting\app\Http\Requests\AboutUsRequest;
use Modules\SystemSetting\app\Services\AboutUsService;

class AboutUsController extends Controller
{
    protected $aboutUsService;

    public function __construct(AboutUsService $aboutUsService)
    {
        $this->aboutUsService = $aboutUsService;
        
        $this->middleware('can:about-us.index')->only(['website', 'mobile', 'update']);
    }

    /**
     * Show website about us form
     */
    public function website()
    {
        $languages = Language::all();
        $aboutUs = $this->aboutUsService->getByPlatform('website');
        $platform = 'website';
        return view('systemsetting::about-us.form', compact('aboutUs', 'languages', 'platform'));
    }

    /**
     * Show mobile about us form
     */
    public function mobile()
    {
        $languages = Language::all();
        $aboutUs = $this->aboutUsService->getByPlatform('mobile');
        $platform = 'mobile';
        return view('systemsetting::about-us.form', compact('aboutUs', 'languages', 'platform'));
    }

    /**
     * Update about us
     */
    public function update(AboutUsRequest $request, $lang, $code, string $platform)
    {
        try {
            $data = $request->validated();
            $aboutUs = $this->aboutUsService->update($data, $platform);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::about-us.updated_successfully'),
                    'data' => $aboutUs
                ]);
            }

            return redirect()
                ->route('admin.system-settings.about-us.' . $platform)
                ->with('success', __('systemsetting::about-us.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::about-us.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::about-us.error_updating') . ': ' . $e->getMessage());
        }
    }
}
