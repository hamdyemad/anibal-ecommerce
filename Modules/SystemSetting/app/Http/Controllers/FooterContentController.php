<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Models\FooterContent;
use Modules\SystemSetting\app\Services\FooterContentService;

class FooterContentController extends Controller
{
    protected $footerContentService;

    public function __construct(FooterContentService $footerContentService)
    {
        $this->footerContentService = $footerContentService;
    }

    /**
     * Display the footer content form
     */
    public function index()
    {
        $languages = Language::all();
        $footerContent = $this->footerContentService->getFooterContent() ?? new FooterContent();

        return view('systemsetting::footer-content.form', compact('languages', 'footerContent'));
    }

    /**
     * Store/Update footer content
     */
    public function store(Request $request)
    {
        try {
            $data = [
                'google_play_link' => $request->input('google_play_link'),
                'apple_store_link' => $request->input('apple_store_link'),
                'active' => $request->input('active', 0),
                'translations' => $request->input('translations', []),
            ];

            $footerContent = $this->footerContentService->saveFooterContent($data);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::footer_content.saved_successfully'),
                    'redirect' => route('admin.system-settings.footer-content.index')
                ]);
            }

            return redirect()
                ->route('admin.system-settings.footer-content.index')
                ->with('success', __('systemsetting::footer_content.saved_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::footer_content.error_saving') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::footer_content.error_saving') . ': ' . $e->getMessage());
        }
    }
}
