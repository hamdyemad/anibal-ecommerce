<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Actions\FeatureAction;
use Modules\SystemSetting\app\Http\Requests\FeatureRequest;
use Modules\SystemSetting\app\Models\Feature;
use Modules\SystemSetting\app\Services\FeatureService;
use Yajra\DataTables\Facades\DataTables;

class FeatureController extends Controller
{
    protected $featureService;
    protected $featureAction;

    public function __construct(FeatureService $featureService, FeatureAction $featureAction)
    {
        $this->featureService = $featureService;
        $this->featureAction = $featureAction;
    }

    /**
     * Display the features form (create/update)
     */
    public function index()
    {
        $languages = Language::all();
        $features = Feature::with(['translations', 'attachments'])
            ->orderBy('id', 'asc')
            ->limit(3)
            ->get();
        // Ensure we always have exactly 3 feature slots
        $featureArray = [];
        for ($i = 0; $i < 3; $i++) {
            if (isset($features[$i])) {
                $featureArray[] = $features[$i];
            } else {
                $featureArray[] = new Feature();
            }
        }

        $features = collect($featureArray);

        return view('systemsetting::features.form', compact('languages', 'features'));
    }


    /**
     * Store/Update all features at once
     */
    public function store(Request $request)
    {
        try {
            $features = $request->input('features', []);

            foreach ($features as $index => $featureData) {
                if (isset($featureData['id']) && $featureData['id']) {
                    // Update existing feature
                    $data = [
                        'active' => $featureData['active'] ?? 0,
                        'translations' => $featureData['translations'] ?? [],
                        'logo' => $request->file("features.{$index}.logo"),
                    ];
                    $this->featureService->updateFeature($featureData['id'], $data);
                } else {
                    // Create new feature
                    $data = [
                        'active' => $featureData['active'] ?? 1,
                        'translations' => $featureData['translations'] ?? [],
                        'logo' => $request->file("features.{$index}.logo"),
                    ];
                    $this->featureService->createFeature($data);
                }
            }

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::features.saved_successfully'),
                    'redirect' => route('admin.system-settings.features.index')
                ]);
            }

            return redirect()
                ->route('admin.system-settings.features.index')
                ->with('success', __('systemsetting::features.saved_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::features.error_saving') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::features.error_saving') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $code, $id)
    {
        $feature = $this->featureService->getFeatureById($id);
        $languages = Language::all();
        return view('systemsetting::features.view', compact('feature', 'languages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $code, $id)
    {
        $feature = $this->featureService->getFeatureById($id);
        $languages = Language::all();
        return view('systemsetting::features.form', compact('feature', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeatureRequest $request, $lang, $code, $id)
    {
        try {
            $data = $request->validated();
            $data['logo'] = $request->file('logo');

            $feature = $this->featureService->updateFeature($id, $data);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::features.updated_successfully'),
                    'redirect' => route('admin.system-settings.features.index'),
                    'data' => $feature
                ]);
            }

            return redirect()
                ->route('admin.system-settings.features.index')
                ->with('success', __('systemsetting::features.updated_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::features.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::features.error_updating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $code, $id)
    {
        try {
            $this->featureService->deleteFeature($id);

            return response()->json([
                'success' => true,
                'message' => __('systemsetting::features.deleted_successfully'),
                'redirect' => route('admin.system-settings.features.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('systemsetting::features.error_deleting') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}
