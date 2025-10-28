<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\CountryService;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CountryApiController extends Controller
{
    protected $countryService;
    protected $languageService;

    public function __construct(CountryService $countryService, LanguageService $languageService)
    {
        $this->countryService = $countryService;
        $this->languageService = $languageService;
    }

    /**
     * Display a listing of countries
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->get('search'),
                'active' => $request->get('active'),
                'created_date_from' => $request->get('created_date_from'),
                'created_date_to' => $request->get('created_date_to'),
            ];

            $perPage = $request->get('per_page', 15);
            $countries = $this->countryService->getAllCountries($filters, $perPage);

            return response()->json([
                'success' => true,
                'message' => 'Countries retrieved successfully',
                'data' => $countries
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving countries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified country
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $country = $this->countryService->getCountryById($id);

            return response()->json([
                'success' => true,
                'message' => 'Country retrieved successfully',
                'data' => $country
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Store a newly created country
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Get all languages for validation
            $languages = $this->languageService->getAll();
            $rules = ['active' => 'required|boolean'];

            foreach ($languages as $language) {
                $rules["translations.{$language->id}.name"] = 'required|string|max:255';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $country = $this->countryService->createCountry($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Country created successfully',
                'data' => $country
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating country',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified country
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Get all languages for validation
            $languages = $this->languageService->getAll();
            $rules = ['active' => 'required|boolean'];

            foreach ($languages as $language) {
                $rules["translations.{$language->id}.name"] = 'required|string|max:255';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $country = $this->countryService->updateCountry($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Country updated successfully',
                'data' => $country
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating country',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified country
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->countryService->deleteCountry($id);

            return response()->json([
                'success' => true,
                'message' => 'Country deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting country',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all active countries (for dropdowns)
     *
     * @return JsonResponse
     */
    public function active(): JsonResponse
    {
        try {
            $countries = $this->countryService->getActiveCountries()
                ->map(function ($country) {
                    return [
                        'id' => $country->id,
                        'name' => $country->getTranslation('name', app()->getLocale()),
                        'name_en' => $country->getTranslation('name', 'en'),
                        'name_ar' => $country->getTranslation('name', 'ar'),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Active countries retrieved successfully',
                'data' => $countries
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving active countries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle country activation status
     *
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $country = $this->countryService->getCountryById($id);
            $newStatus = !$country->active;
            
            $this->countryService->updateCountry($id, ['active' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Country status updated successfully',
                'data' => [
                    'id' => $id,
                    'active' => $newStatus
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating country status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
