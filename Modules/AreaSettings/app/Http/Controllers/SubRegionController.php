<?php

namespace Modules\AreaSettings\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\AreaSettings\app\Http\Requests\SubRegionRequest;
use Modules\AreaSettings\app\Services\SubRegionService;
use Modules\AreaSettings\app\Services\RegionService;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\AreaSettings\app\Resources\RegionResource;

class SubRegionController extends Controller
{
    protected $subregionService;
    protected $regionService;
    protected $languageService;

    public function __construct(
        SubRegionService $subregionService,
        RegionService $regionService,
        LanguageService $languageService
    ) {
        $this->subregionService = $subregionService;
        $this->regionService = $regionService;
        $this->languageService = $languageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();

        // Get all regions for filter dropdown
        $regions = RegionResource::collection($this->regionService->getAllRegions())->resolve();

        return view('areasettings::subregion.index', compact(
            'regions',
            'languages'
        ));
    }

    /**
     * Get subregions data for DataTables AJAX
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
        
        // Get sorting parameters - handle both DataTables default format and custom parameters
        $orderColumnIndex = $request->get('order_column');
        $orderDirection = $request->get('order_dir', 'desc');
        
        if ($orderColumnIndex === null && $request->has('order')) {
            $orderData = $request->get('order');
            if (is_array($orderData) && isset($orderData[0])) {
                $orderColumnIndex = $orderData[0]['column'] ?? 0;
                $orderDirection = $orderData[0]['dir'] ?? 'desc';
            }
        }
        
        $orderColumnIndex = $orderColumnIndex ?? 0;

        // Get filter parameters
        $regionId = $request->get('region_id');
        $active = $request->get('active');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'region_id' => $regionId,
            'active' => $active,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Debug logging
        Log::info('SubRegion Datatable Filters:', [
            'filters' => $filters,
            'order_column' => $orderColumnIndex,
            'order_dir' => $orderDirection
        ]);

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total records before filtering
        $totalRecords = $this->subregionService->getSubRegionsQuery([])->count();

        // Get subregions with filters - Clone query for counting
        $baseQuery = $this->subregionService->getSubRegionsQuery($filters);
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
                (count($languages) + 1) => 'region_id',
                (count($languages) + 2) => 'active',
                (count($languages) + 3) => 'created_at',
            ];

            if (isset($orderColumns[$orderColumnIndex])) {
                $orderBy = $orderColumns[$orderColumnIndex];
            }
        }

        // Get sub-regions with sorting applied
        $sortedQuery = $this->subregionService->getSubRegionsQuery($filters, $orderBy, $orderDirection);
        
        // Apply pagination
        $perPage = $request->get('per_page', $request->get('length', 15));
        $page = $request->get('page', 1);
        $subregions = $sortedQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = [];
        foreach ($subregions as $index => $subregion) {
            $row = [];
            $row[] = ($subregions->currentPage() - 1) * $subregions->perPage() + $index + 1; // Row number with pagination offset

            // Add name columns for each language
            foreach ($languages as $language) {
                $name = $subregion->getTranslation('name', $language->code) ?? '-';
                $row[] = '<div class="userDatatable-content" ' . ($language->rtl ? 'dir="rtl"' : '') . '>
                            <strong>' . e($name) . '</strong>
                          </div>';
            }

            // Region name
            $regionName = $subregion->region ? $subregion->region->getTranslation('name', app()->getLocale()) : '-';
            $row[] = '<div class="userDatatable-content">' . e($regionName) . '</div>';

            // Active status
            $activeHtml = '<div class="userDatatable-content">';
            if ($subregion->active) {
                $activeHtml .= '<span class="badge badge-success" style="border-radius: 6px; padding: 6px 12px;">
                                    <i class="uil uil-check me-1"></i>' . e(__('areas/subregion.active')) . '
                                </span>';
            } else {
                $activeHtml .= '<span class="badge badge-danger" style="border-radius: 6px; padding: 6px 12px;">
                                    <i class="uil uil-times me-1"></i>' . e(__('areas/subregion.inactive')) . '
                                </span>';
            }
            $activeHtml .= '</div>';
            $row[] = $activeHtml;

            // Created at
            $row[] = '<div class="userDatatable-content">' . e($subregion->created_at->format('Y-m-d H:i')) . '</div>';

            // Actions
            $actionsHtml = '<ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                                <li>
                                    <a href="' . route('admin.area-settings.subregions.show', $subregion->id) . '" class="view" title="' . e(trans('common.view')) . '">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="' . route('admin.area-settings.subregions.edit', $subregion->id) . '" class="edit" title="' . e(trans('common.edit')) . '">
                                        <i class="uil uil-edit"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" 
                                       class="remove" 
                                       title="' . e(trans('common.delete')) . '"
                                       data-bs-toggle="modal" 
                                       data-bs-target="#modal-delete-subregion"
                                       data-item-id="' . $subregion->id . '"
                                       data-item-name="' . e($subregion->name_en ?? '') . '">
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
            'current_page' => $subregions->currentPage(),
            'last_page' => $subregions->lastPage(),
            'per_page' => $subregions->perPage(),
            'total' => $subregions->total(),
            'from' => $subregions->firstItem(),
            'to' => $subregions->lastItem()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $languages = $this->languageService->getAll();
            $selectedRegionId = $request->get('region_id');
            $regions = RegionResource::collection($this->regionService->getAllRegions())->resolve();
            
            return view('areasettings::subregion.form', compact('languages', 'regions', 'selectedRegionId'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.subregions.index')
                ->with('error', __('Error loading form'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubRegionRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->subregionService->createSubRegion($validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('areasettings::subregion.subregion_created'),
                    'redirect' => route('admin.area-settings.subregions.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.subregions.index')
                ->with('success', __('areasettings::subregion.subregion_created'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('areasettings::subregion.error_creating_subregion') . ': ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('areasettings::subregion.error_creating_subregion') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $subregion = $this->subregionService->getSubRegionById($id);
            return view('areasettings::subregion.view', compact('subregion'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.subregions.index')
                ->with('error', __('areasettings::subregion.subregion_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $regions = RegionResource::collection($this->regionService->getAllRegions())->resolve();
            $subregion = $this->subregionService->getSubRegionById($id);
            return view('areasettings::subregion.form', compact('subregion', 'languages', 'regions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.subregions.index')
                ->with('error', __('areasettings::subregion.subregion_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubRegionRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->subregionService->updateSubRegion($id, $validated);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('areasettings::subregion.subregion_updated'),
                    'redirect' => route('admin.area-settings.subregions.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.subregions.index')
                ->with('success', __('areasettings::subregion.subregion_updated'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('areasettings::subregion.error_updating_subregion') . ': ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('areasettings::subregion.error_updating_subregion') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->subregionService->deleteSubRegion($id);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('areasettings::subregion.subregion_deleted'),
                    'redirect' => route('admin.area-settings.subregions.index')
                ]);
            }
            
            return redirect()->route('admin.area-settings.subregions.index')
                ->with('success', __('areasettings::subregion.subregion_deleted'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('areasettings::subregion.error_deleting_subregion') . ': ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->route('admin.area-settings.subregions.index')
                ->with('error', __('areasettings::subregion.error_deleting_subregion') . ': ' . $e->getMessage());
        }
    }
}
