<?php

namespace Modules\Vendor\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Vendor\app\Services\VendorService;
use App\Services\LanguageService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Modules\AreaSettings\app\Resources\CountryResource;
use Modules\AreaSettings\app\Services\CountryService;
use Modules\CategoryManagment\app\Http\Resources\ActivityResource;
use Modules\CategoryManagment\app\Services\ActivityService;
use Modules\Vendor\app\Http\Requests\Vendor\VendorRequest;

class VendorController extends Controller {

    public function __construct(
        protected VendorService $vendorService,
        protected CountryService $countryService,
        protected ActivityService $activityService,
        protected LanguageService $languageService
    ) {}

    public function index() {
        $languages = $this->languageService->getAll();
        return view('vendor::vendors.index', compact('languages'));
    }

    public function datatable() {
        try {
            // Get paginated vendors and extract the items
            $vendorsPaginated = $this->vendorService->getAllVendors([]);
            $vendors = $vendorsPaginated->items(); // Extract items from paginator
            $languages = $this->languageService->getAll();
            
            $data = [];
            foreach ($vendors as $vendor) {
                $row = [];
                
                // ID
                $row[] = $vendor->id;
                
                // Names for each language
                foreach ($languages as $language) {
                    $translation = $vendor->translations()
                        ->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    
                    $name = $translation ? $translation->lang_value : '-';
                    $row[] = '<div class="userDatatable-content"' . ($language->rtl ? ' dir="rtl"' : '') . '>' . htmlspecialchars($name) . '</div>';
                }
                
                // Email
                $email = $vendor->user ? $vendor->user->email : '-';
                $row[] = '<div class="userDatatable-content">' . htmlspecialchars($email) . '</div>';
                
                // Country
                if ($vendor->country) {
                    $countryTranslation = $vendor->country->translations()
                        ->where('lang_id', app()->getLocale() === 'ar' ? 2 : 1)
                        ->where('lang_key', 'name')
                        ->first();
                    $countryName = $countryTranslation ? $countryTranslation->lang_value : ($vendor->country->code ?? '-');
                } else {
                    $countryName = '-';
                }
                $row[] = '<div class="userDatatable-content">' . htmlspecialchars($countryName) . '</div>';
                
                // Activities
                $activitiesArray = [];
                if($vendor->activities) {
                    foreach($vendor->activities as $activity) {
                        $activityTranslation = $activity->getTranslation('name', app()->getLocale());
                        $activitiesArray[] = $activityTranslation ? $activityTranslation : $activity->name;
                    }
                }
                $activitiesHtml = !empty($activitiesArray) 
                    ? implode('', array_map(fn($act) => '<span class="badge badge-round badge-primary badge-lg me-1">' . htmlspecialchars($act) . '</span>', $activitiesArray))
                    : '-';
                $row[] = '<div class="userDatatable-content">' . $activitiesHtml . '</div>';
                
                // Active Status - check if property exists
                $isActive = isset($vendor->active) ? $vendor->active : true;
                $statusBadge = $isActive
                    ? '<span class="badge badge-success">Active</span>' 
                    : '<span class="badge badge-danger">Inactive</span>';
                $row[] = '<div class="userDatatable-content">' . $statusBadge . '</div>';
                
                // Created At
                $createdAt = $vendor->created_at ? $vendor->created_at->format('Y-m-d H:i') : '-';
                $row[] = '<div class="userDatatable-content">' . $createdAt . '</div>';
                
                // Actions - safely get vendor name
                $vendorName = 'Vendor';
                $nameTranslation = $vendor->translations()->where('lang_key', 'name')->first();
                if ($nameTranslation && $nameTranslation->lang_value) {
                    $vendorName = $nameTranslation->lang_value;
                }
                
                $actions = '
                    <ul class="orderDatatable_actions mb-0 d-flex flex-wrap">
                        <li>
                            <a href="' . route('admin.vendors.show', $vendor->id) . '" class="view">
                                <i class="uil uil-eye"></i>
                            </a>
                        </li>
                        <li>
                            <a href="' . route('admin.vendors.edit', $vendor->id) . '" class="edit">
                                <i class="uil uil-edit"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" 
                               class="remove" 
                               data-bs-toggle="modal" 
                               data-bs-target="#modal-delete-vendor"
                               data-item-id="' . $vendor->id . '"
                               data-item-name="' . htmlspecialchars($vendorName) . '">
                                <i class="uil uil-trash-alt"></i>
                            </a>
                        </li>
                    </ul>
                ';
                $row[] = $actions;
                
                $data[] = $row;
            }
            
            return response()->json(['data' => $data]);
        } catch (Exception $e) {
            Log::error('Vendor datatable error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }

    public function create() {
        // Get all countries and activities for select dropdowns
        $countriesData = $this->countryService->getAllCountries([], 1000);
        $activitiesData = $this->activityService->getAllActivities([], 1000);
        
        // Extract items from paginated results and transform to arrays
        $countries = CountryResource::collection($countriesData)->resolve();
        $activities = ActivityResource::collection($activitiesData)->resolve();
        
        // Get languages for translations
        $languages = $this->languageService->getAll();
        
        return view('vendor::vendors.form', compact('countries', 'activities', 'languages'));
    }

    public function store(VendorRequest $request)
    {
        try {
            $vendor = $this->vendorService->createVendor($request->validated());
            // Check if it's an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('vendor::vendor.vendor_created_successfully'),
                    'redirect' => route('admin.vendors.index'),
                    'vendor' => $vendor
                ]);
            }

            return redirect()
                ->route('admin.vendors.index')
                ->with('success', __('vendor::vendor.vendor_created_successfully'));
        } catch (Exception $e) {
            Log::error("Vendor creation failed", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if it's an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('vendor::vendor.error_creating_vendor'),
                    'error_details' => $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('vendor::vendor.error_creating_vendor'))
                ->with('error_details', $e->getMessage());
        }
    }

    public function show($id) {
        $vendor = $this->vendorService->getVendorById($id);
        $languages = $this->languageService->getAll();
        return view('vendor::vendors.show', compact('vendor', 'languages'));
    }

    public function edit($id) {
        $vendor = $this->vendorService->getVendorById($id);
        // Get all countries and activities for select dropdowns
        $countriesData = $this->countryService->getAllCountries([], 1000);
        $activitiesData = $this->activityService->getAllActivities([], 1000);
        
        // Extract items from paginated results and transform to arrays
        $countries = CountryResource::collection($countriesData)->resolve();
        $activities = ActivityResource::collection($activitiesData)->resolve();
        // Get languages for translations
        $languages = $this->languageService->getAll();
        return view('vendor::vendors.form', compact('vendor', 'countries', 'activities', 'languages'));
    }

    public function update(VendorRequest $request, $id) {
        try {
            $this->vendorService->updateVendor($id, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => __('vendor::vendor.vendor_updated_successfully'),
                'redirect' => route('admin.vendors.index')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Vendor update failed', [
                'vendor_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('vendor::vendor.error_updating_vendor'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id) {
        try {
            $this->vendorService->deleteVendor($id);
            
            return response()->json([
                'success' => true,
                'message' => __('vendor::vendor.vendor_deleted_successfully')
            ]);
        } catch (Exception $e) {
            Log::error('Vendor deletion failed', [
                'vendor_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('vendor::vendor.error_deleting_vendor'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

}
