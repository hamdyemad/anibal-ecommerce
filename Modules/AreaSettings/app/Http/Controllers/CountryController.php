<?php

namespace Modules\AreaSettings\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Actions\CountryAction;
use Modules\AreaSettings\app\Http\Requests\CountryRequest;
use Modules\AreaSettings\app\Services\CountryService;
use App\Services\LanguageService;

class CountryController extends Controller
{

    public function __construct(
        protected CountryService $countryService, 
        protected LanguageService $languageService,
        protected CountryAction $countryAction
    )
    {
        $this->middleware('can:area.country.index')->only(['index']);
        $this->middleware('can:area.country.show')->only(['show']);
        $this->middleware('can:area.country.create')->only(['create', 'store']);
        $this->middleware('can:area.country.edit')->only(['edit', 'update']);
        $this->middleware('can:area.country.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();
        return view('areasettings::country.index', compact('languages'));
    }

    /**
     * Get countries data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = $this->countryAction->getDatatableData($request);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        return view('areasettings::country.form', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->countryService->createCountry($validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Country created successfully'),
                    'redirect' => route('admin.area-settings.countries.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.countries.index')
                ->with('success', __('Country created successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating country: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating country: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $country = $this->countryService->getCountryById($id);
            return view('areasettings::country.view', compact('country', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.countries.index')
                ->with('error', __('Country not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $country = $this->countryService->getCountryById($id);
            return view('areasettings::country.form', compact('country', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.countries.index')
                ->with('error', __('Country not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->countryService->updateCountry($id, $validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Country updated successfully'),
                    'redirect' => route('admin.area-settings.countries.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.countries.index')
                ->with('success', __('Country updated successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating country: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating country: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->countryService->deleteCountry($id);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Country deleted successfully'),
                    'redirect' => route('admin.area-settings.countries.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.countries.index')
                ->with('success', __('Country deleted successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error deleting country: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->route('admin.area-settings.countries.index')
                ->with('error', __('Error deleting country: ') . $e->getMessage());
        }
    }
}
