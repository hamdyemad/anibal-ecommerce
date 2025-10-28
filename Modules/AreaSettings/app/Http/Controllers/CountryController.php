<?php

namespace Modules\AreaSettings\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Http\Requests\CountryRequest;
use Modules\AreaSettings\app\Services\CountryService;
use App\Services\LanguageService;

class CountryController extends Controller
{

    public function __construct(protected CountryService $countryService, protected LanguageService $languageService)
    {
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
        $active = $request->get('active');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'active' => $active,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total records before filtering
        $totalRecords = $this->countryService->getCountriesQuery([])->count();

        // Get countries with filters - Clone query for counting
        $baseQuery = $this->countryService->getCountriesQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();

        // Prepare sorting parameters
        $orderBy = null;
        $sortBy = $request->get('sort_by');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if ($sortBy) {
            // Handle name sorting by language ID
            if (strpos($sortBy, 'name_') === 0) {
                $languageId = str_replace('name_', '', $sortBy);
                $orderBy = ['lang_id' => $languageId];
            } else {
                // Handle regular column sorting
                $orderBy = $sortBy;
            }
        } else {
            // Fallback to DataTables format if no sort_by parameter
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
                    (count($languages) + 1) => 'code',
                    (count($languages) + 2) => 'phone_code',
                    (count($languages) + 3) => 'active',
                    (count($languages) + 4) => 'created_at',
                ];

                if (isset($orderColumns[$orderColumnIndex])) {
                    $orderBy = $orderColumns[$orderColumnIndex];
                }
            }
        }

        // Get countries with sorting applied
        $sortedQuery = $this->countryService->getCountriesQuery($filters, $orderBy, $sortDirection);
        
        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $countries = $sortedQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data as arrays for DataTables
        $data = [];
        foreach ($countries as $country) {
            $row = [];
            
            // ID
            $row[] = $country->id;
            
            // Names for each language
            foreach ($languages as $language) {
                $translation = $country->translations()
                    ->where('lang_id', $language->id)
                    ->where('lang_key', 'name')
                    ->first();
                
                $name = $translation ? $translation->lang_value : '-';
                $row[] = '<div class="userDatatable-content"' . ($language->rtl ? ' dir="rtl"' : '') . '>' . htmlspecialchars($name) . '</div>';
            }
            
            // Country Code
            $row[] = '<div class="userDatatable-content">' . htmlspecialchars($country->code) . '</div>';
            
            // Phone Code
            $row[] = '<div class="userDatatable-content">' . htmlspecialchars($country->phone_code) . '</div>';
            
            // Active Status
            $isActive = $country->active ?? true;
            $statusBadge = $isActive
                ? '<span class="badge badge-success badge-lg badge-round">' . __('areasettings::country.active') . '</span>' 
                : '<span class="badge badge-danger badge-lg badge-round">' . __('areasettings::country.inactive') . '</span>';
            $row[] = '<div class="userDatatable-content">' . $statusBadge . '</div>';
            
            // Created At
            $createdAt = $country->created_at ? $country->created_at->format('Y-m-d H:i') : '-';
            $row[] = '<div class="userDatatable-content">' . $createdAt . '</div>';
            
            // Actions
            $countryName = 'Country';
            $nameTranslation = $country->translations()->where('lang_key', 'name')->first();
            if ($nameTranslation && $nameTranslation->lang_value) {
                $countryName = $nameTranslation->lang_value;
            }
            
            $actions = '
                <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                    <li>
                        <a href="' . route('admin.area-settings.countries.show', $country->id) . '" class="view">
                            <i class="uil uil-eye"></i>
                        </a>
                    </li>
                    <li>
                        <a href="' . route('admin.area-settings.countries.edit', $country->id) . '" class="edit">
                            <i class="uil uil-edit"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" 
                           class="remove" 
                           data-bs-toggle="modal" 
                           data-bs-target="#modal-delete-country"
                           data-item-id="' . $country->id . '"
                           data-item-name="' . htmlspecialchars($countryName) . '">
                            <i class="uil uil-trash-alt"></i>
                        </a>
                    </li>
                </ul>
            ';
            $row[] = $actions;
            
            $data[] = $row;
        }

        
        return response()->json([
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'current_page' => $countries->currentPage(),
            'last_page' => $countries->lastPage(),
            'per_page' => $countries->perPage(),
            'total' => $countries->total(),
            'from' => $countries->firstItem(),
            'to' => $countries->lastItem()
        ]);
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
