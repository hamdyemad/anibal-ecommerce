<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SystemSetting\app\Http\Requests\PointsSettingRequest;
use Modules\SystemSetting\app\Services\PointsSettingService;
use Modules\SystemSetting\app\Interfaces\PointsSettingRepositoryInterface;
use Modules\SystemSetting\app\Models\Currency;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Services\CurrencyService;

class PointsSettingController extends Controller
{
    public function __construct(
        protected PointsSettingService $pointsSettingService,
        protected PointsSettingRepositoryInterface $pointsSettingRepository,
        protected CurrencyService $currencyService
    ) {}

    /**
     * Display points settings form (single form for all currencies)
     */
    public function index()
    {
        $currencies = $this->currencyService->getAllCurrencies();
        $pointsSettings = $this->pointsSettingService->getAllSettings([], 0);
        $pointSystem = $this->pointsSettingService->getPointSystem();
        $data = [
            'title' => trans('systemsetting::points.points_system_settings'),
            'currencies' => $currencies,
            'pointsSettings' => $pointsSettings,
            'pointSystem' => $pointSystem,
        ];

        return view('systemsetting::points.form', $data);
    }


    /**
     * Store a newly created points setting
     */
    public function store($lang, $countryCode, PointsSettingRequest $request)
    {
        try {
            $validated = $request->validated();

            // Check if setting already exists for this currency
            $existing = $this->pointsSettingRepository->getSettingByCurrencyId($validated['currency_id']);

            if ($existing) {
                // Update existing setting
                $pointsSetting = $this->pointsSettingService->updateSetting($existing->id, $validated);
            } else {
                // Create new setting
                $pointsSetting = $this->pointsSettingService->createSetting($validated);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('systemsetting::points.points_setting_updated'),
                    'data' => $pointsSetting,
                ]);
            }

            return redirect()->route('admin.points-settings.index')
                ->with('success', trans('systemsetting::points.points_setting_created'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('systemsetting::points.error_creating_setting') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', trans('systemsetting::points.error_creating_setting') . ': ' . $e->getMessage());
        }
    }


    /**
     * Update the specified points setting
     */
    public function update($lang, $countryCode, PointsSettingRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $pointsSetting = $this->pointsSettingService->updateSetting($id, $validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('systemsetting::points.points_setting_updated'),
                    'data' => $pointsSetting,
                ]);
            }

            return redirect()->route('admin.points-settings.index')
                ->with('success', trans('systemsetting::points.points_setting_updated'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('systemsetting::points.error_updating_setting') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', trans('systemsetting::points.error_updating_setting') . ': ' . $e->getMessage());
        }
    }


    /**
     * Toggle points system enabled status
     */
    public function togglePointsSystemEnabled(Request $request, $lang, $countryCode)
    {
        try {
            $pointSystem = $this->pointsSettingService->togglePointsSystemEnabled();

            return response()->json([
                'success' => true,
                'message' => trans('systemsetting::points.points_system_updated'),
                'is_enabled' => $pointSystem->is_enabled,
                'data' => $pointSystem
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('systemsetting::points.error_updating_setting') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}
