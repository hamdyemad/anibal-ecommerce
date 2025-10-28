<?php

namespace Modules\AreaSettings\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\AreaSettings\app\Http\Requests\RegionRequest;
use Modules\AreaSettings\app\Services\RegionService;
use Modules\AreaSettings\app\Services\CityService;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Resources\CityResource;

class RegionController extends Controller
{
    protected $regionService;
    protected $cityService;
    protected $languageService;

    public function __construct(
        RegionService $regionService,
        CityService $cityService,
        LanguageService $languageService
    ) {
        $this->regionService = $regionService;
        $this->cityService = $cityService;
        $this->languageService = $languageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();

        // Get all cities for filter dropdown
        $cities = CityResource::collection($this->cityService->getAllCities())->resolve();

        // Initialize filter parameters for view
        $cityId = null;
        $active = null;
        $search = null;
        $dateFrom = null;
        $dateTo = null;
        
        // Empty paginated result for backward compatibility
        $regions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        return view('areasettings::region.index', compact(
            'regions',
            'cities',
            'languages',
            'search',
            'cityId',
            'active',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Get regions data for DataTables AJAX
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
        $cityId = $request->get('city_id');
        $active = $request->get('active');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'country_id' => $countryId,
            'city_id' => $cityId,
            'active' => $active,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total records before filtering
        $totalRecords = $this->regionService->getRegionsQuery([])->count();

        // Get regions with filters - Clone query for counting
        $baseQuery = $this->regionService->getRegionsQuery($filters);
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
                (count($languages) + 1) => 'city_id',
                (count($languages) + 2) => 'id', // subregions count (not sortable)
                (count($languages) + 3) => 'active',
                (count($languages) + 4) => 'created_at',
            ];

            if (isset($orderColumns[$orderColumnIndex])) {
                $orderBy = $orderColumns[$orderColumnIndex];
            }
        }

        // Get regions with sorting applied
        $sortedQuery = $this->regionService->getRegionsQuery($filters, $orderBy, $orderDirection);
        
        // Apply pagination
        $perPage = $request->get('per_page', $request->get('length', 15));
        $page = $request->get('page', 1);
        $regions = $sortedQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = [];
        foreach ($regions as $index => $region) {
            $row = [];
            $row[] = ($regions->currentPage() - 1) * $regions->perPage() + $index + 1; // Row number with pagination offset

            // Add name columns for each language
            foreach ($languages as $language) {
                $name = $region->getTranslation('name', $language->code) ?? '-';
                $row[] = '<div class="userDatatable-content" ' . ($language->rtl ? 'dir="rtl"' : '') . '>
                            <strong>' . e($name) . '</strong>
                          </div>';
            }

            // City name
            $cityName = $region->city ? $region->city->getTranslation('name', app()->getLocale()) : '-';
            $row[] = '<div class="userDatatable-content">' . e($cityName) . '</div>';

            // Subregions count
            $subregionsCount = $region->subregions()->count();
            $row[] = '<div class="userDatatable-content">
                        <span class="badge badge-primary" style="border-radius: 6px; padding: 6px 12px;">
                            <i class="uil uil-map-marker me-1"></i>' . $subregionsCount . '
                        </span>
                      </div>';

            // Active status
            $activeHtml = '<div class="userDatatable-content">';
            if ($region->active) {
                $activeHtml .= '<span class="badge badge-success" style="border-radius: 6px; padding: 6px 12px;">
                                    <i class="uil uil-check me-1"></i>' . e(__('areasettings::region.active')) . '
                                </span>';
            } else {
                $activeHtml .= '<span class="badge badge-danger" style="border-radius: 6px; padding: 6px 12px;">
                                    <i class="uil uil-times me-1"></i>' . e(__('areasettings::region.inactive')) . '
                                </span>';
            }
            $activeHtml .= '</div>';
            $row[] = $activeHtml;

            // Created at
            $row[] = '<div class="userDatatable-content">' . e($region->created_at->format('Y-m-d H:i')) . '</div>';

            // Actions
            $actionsHtml = '<ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                                <li>
                                    <a href="' . route('admin.area-settings.regions.show', $region->id) . '" class="view" title="' . e(trans('common.view')) . '">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="' . route('admin.area-settings.regions.edit', $region->id) . '" class="edit" title="' . e(trans('common.edit')) . '">
                                        <i class="uil uil-edit"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" 
                                       class="remove" 
                                       title="' . e(trans('common.delete')) . '"
                                       data-bs-toggle="modal" 
                                       data-bs-target="#modal-delete-region"
                                       data-item-id="' . $region->id . '"
                                       data-item-name="' . e($region->name_en ?? '') . '">
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
            'current_page' => $regions->currentPage(),
            'last_page' => $regions->lastPage(),
            'per_page' => $regions->perPage(),
            'total' => $regions->total(),
            'from' => $regions->firstItem(),
            'to' => $regions->lastItem()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $cities = CityResource::collection($this->cityService->getActiveCities())->resolve();
        try {
            $languages = $this->languageService->getAll();
            $selectedCityId = $request->get('city_id');
            
            return view('areasettings::region.form', compact('languages', 'cities', 'selectedCityId'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.regions.index')
                ->with('error', __('Error loading form'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegionRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->regionService->createRegion($validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Region created successfully'),
                    'redirect' => route('admin.area-settings.regions.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.regions.index')
                ->with('success', __('Region created successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating region: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating region: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $region = $this->regionService->getRegionById($id);
            return view('areasettings::region.view', compact('region'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.regions.index')
                ->with('error', __('Region not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $cities = CityResource::collection($this->cityService->getActiveCities())->resolve();
            $region = $this->regionService->getRegionById($id);
            return view('areasettings::region.form', compact('region', 'languages', 'cities'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.regions.index')
                ->with('error', __('Region not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RegionRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->regionService->updateRegion($id, $validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Region updated successfully'),
                    'redirect' => route('admin.area-settings.regions.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.regions.index')
                ->with('success', __('Region updated successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating region: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating region: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->regionService->deleteRegion($id);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Region deleted successfully'),
                    'redirect' => route('admin.area-settings.regions.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.regions.index')
                ->with('success', __('Region deleted successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error deleting region: ') . $e->getMessage()
                ], 422);
            }
            
            return redirect()->route('admin.area-settings.regions.index')
                ->with('error', __('Error deleting region: ') . $e->getMessage());
        }
    }
}
