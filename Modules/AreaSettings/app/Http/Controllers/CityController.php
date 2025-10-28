<?php

namespace Modules\AreaSettings\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\AreaSettings\app\Services\CityService;
use Modules\AreaSettings\app\Http\Requests\CityRequest;
use App\Services\LanguageService;
use Modules\AreaSettings\app\Services\CountryService;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Resources\CountryResource;

class CityController extends Controller
{

    public function __construct(
        protected CityService $cityService, 
        protected CountryService $countryService,
        protected LanguageService $languageService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();
        
        // Get countries for filter dropdown
        $countries = $this->countryService->getAllCountries();
        
        // Initialize filter parameters for view (to prevent undefined variable errors)
        $countryId = null;
        $active = null;
        $search = null;
        $dateFrom = null;
        $dateTo = null;
        
        // Empty paginated result for backward compatibility (DataTables will populate via AJAX)
        $cities = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        
        return view('areasettings::city.index', compact('languages', 'countries', 'countryId', 'active', 'search', 'dateFrom', 'dateTo', 'cities'));
    }

    /**
     * Get cities data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        
        // Get search value from custom parameter or DataTables default
        $searchValue = $request->get('search');
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }
        
        $orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
        $orderDirection = $request->get('order')[0]['dir'] ?? 'asc';

        // Get filter parameters
        $countryId = $request->get('country_id');
        $active = $request->get('active');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'country_id' => $countryId,
            'active' => $active,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total records before filtering
        $totalRecords = $this->cityService->getCitiesQuery([])->count();

        // Get cities with filters - Clone query for counting (without sorting)
        $baseQuery = $this->cityService->getCitiesQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();

        // Prepare sorting parameters
        $orderBy = null;
        // Check if sorting by name column (columns 1 to count($languages))
        if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
            // Sort by translated name - pass language ID to repository
            $languageIndex = $orderColumnIndex - 1;
            $language = $languages[$languageIndex];
            $orderBy = ['lang_id' => $language->id];
        } else {
            // Sort by regular columns
            $orderColumns = [
                0 => 'id',
                (count($languages) + 1) => 'country_id',
                (count($languages) + 2) => 'id', // regions count (not sortable by query, using id as placeholder)
                (count($languages) + 3) => 'active',
                (count($languages) + 4) => 'created_at',
            ];

            if (isset($orderColumns[$orderColumnIndex])) {
                $orderBy = $orderColumns[$orderColumnIndex];
            }
        }

        // Get cities with sorting applied
        $sortedQuery = $this->cityService->getCitiesQuery($filters, $orderBy, $orderDirection);
        
        // Apply pagination
        $perPage = $request->get('per_page', $request->get('length', 15));
        $page = $request->get('page', 1);
        $cities = $sortedQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = [];
        foreach ($cities as $index => $city) {
            $row = [];
            $row[] = ($cities->currentPage() - 1) * $cities->perPage() + $index + 1; // Row number with pagination offset

            // Add name columns for each language
            foreach ($languages as $language) {
                $name = $city->getTranslation('name', $language->code) ?? '-';
                $row[] = '<div class="userDatatable-content" ' . ($language->rtl ? 'dir="rtl"' : '') . '>
                            <strong>' . e($name) . '</strong>
                          </div>';
            }

            // Country name
            $countryName = $city->country ? $city->country->getTranslation('name', app()->getLocale()) : '-';
            $row[] = '<div class="userDatatable-content">' . e($countryName) . '</div>';

            // Regions count
            $regionsCount = $city->regions()->count();
            $row[] = '<div class="userDatatable-content">
                        <span class="badge badge-primary" style="border-radius: 6px; padding: 6px 12px;">
                            <i class="uil uil-map-marker me-1"></i>' . $regionsCount . '
                        </span>
                      </div>';

            // Active status
            $activeHtml = '<div class="userDatatable-content">';
            if ($city->active) {
                $activeHtml .= '<span class="badge badge-success" style="border-radius: 6px; padding: 6px 12px;">
                                    <i class="uil uil-check me-1"></i>' . e(__('areasettings::city.active')) . '
                                </span>';
            } else {
                $activeHtml .= '<span class="badge badge-danger" style="border-radius: 6px; padding: 6px 12px;">
                                    <i class="uil uil-times me-1"></i>' . e(__('areasettings::city.inactive')) . '
                                </span>';
            }
            $activeHtml .= '</div>';
            $row[] = $activeHtml;

            // Created at
            $row[] = '<div class="userDatatable-content">' . e($city->created_at->format('Y-m-d H:i')) . '</div>';

            // Actions
            $actionsHtml = '<ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                                <li>
                                    <a href="' . route('admin.area-settings.cities.show', $city->id) . '" class="view" title="' . e(trans('common.view')) . '">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="' . route('admin.area-settings.cities.edit', $city->id) . '" class="edit" title="' . e(trans('common.edit')) . '">
                                        <i class="uil uil-edit"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" 
                                       class="remove" 
                                       title="' . e(trans('common.delete')) . '"
                                       data-bs-toggle="modal" 
                                       data-bs-target="#modal-delete-city"
                                       data-item-id="' . $city->id . '"
                                       data-item-name="' . e($city->name_en ?? '') . '">
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
            'current_page' => $cities->currentPage(),
            'last_page' => $cities->lastPage(),
            'per_page' => $cities->perPage(),
            'total' => $cities->total(),
            'from' => $cities->firstItem(),
            'to' => $cities->lastItem()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $languages = $this->languageService->getAll();
        $countries = CountryResource::collection($this->countryService->getAllCountries())->resolve();
        // Get country_id from query parameter if passed
        $selectedCountryId = $request->get('country_id');
        return view('areasettings::city.form', compact('languages', 'countries', 'selectedCountryId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CityRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->cityService->createCity($validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('City created successfully'),
                    'redirect' => route('admin.area-settings.cities.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.cities.index')
                ->with('success', __('City created successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating city: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating city: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $city = $this->cityService->getCityById($id);
            return view('areasettings::city.view', compact('city'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.cities.index')
                ->with('error', __('City not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $countries = CountryResource::collection($this->countryService->getAllCountries())->resolve();
            $city = $this->cityService->getCityById($id);
            return view('areasettings::city.form', compact('city', 'languages', 'countries'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.cities.index')
                ->with('error', __('City not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CityRequest $request, string $id)
    {
        $validated = $request->validated();
        
        \Log::info('Validated data', ['validated' => $validated]);

        try {
            $this->cityService->updateCity($id, $validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('City updated successfully'),
                    'redirect' => route('admin.area-settings.cities.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.cities.index')
                ->with('success', __('City updated successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating city: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating city: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->cityService->deleteCity($id);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('City deleted successfully'),
                    'redirect' => route('admin.area-settings.cities.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.cities.index')
                ->with('success', __('City deleted successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error deleting city: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->route('admin.area-settings.cities.index')
                ->with('error', __('Error deleting city: ') . $e->getMessage());
        }
    }
}
