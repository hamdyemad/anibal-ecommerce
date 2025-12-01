<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CatalogManagement\app\Http\Requests\OccasionRequest;
use Modules\CatalogManagement\app\Services\OccasionService;
use Modules\CatalogManagement\app\Interfaces\OccasionRepositoryInterface;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class OccasionController extends Controller
{
    public function __construct(
        protected OccasionService $occasionService,
        protected OccasionRepositoryInterface $occasionRepository,
        protected LanguageService $languageService
    ) {}

    /**
     * Display a listing of occasions
     */
    public function index()
    {
        $languages = $this->languageService->getAll();
        $vendors = \Modules\Vendor\app\Models\Vendor::all();
        $data = [
            'title' => trans('catalogmanagement::occasion.occasions_management'),
            'languages' => $languages,
            'vendors' => $vendors,
        ];
        return view('catalogmanagement::occasions.index', $data);
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable(Request $request)
    {
        try {
            // Get filters from request
            $filters = [
                'search' => $request->get('search')['value'] ?? null,
                'active' => $request->get('active'),
                'created_from' => $request->get('created_from'),
                'created_until' => $request->get('created_until'),
            ];

            // Get occasions query with filters
            $query = $this->occasionService->getOccasionsQuery($filters);

            return DataTables::of($query)
                ->addColumn('name', function ($occasion) {
                    $translations = [];
                    foreach ($occasion->translations as $translation) {
                        $translations[$translation->locale] = $translation->name;
                    }
                    return $translations;
                })
                ->addColumn('vendor', function ($occasion) {
                    return $occasion->vendor ? $occasion->vendor->name : '-';
                })
                ->addColumn('image', function ($occasion) {
                    return $occasion->image ? asset('storage/' . $occasion->image) : null;
                })
                ->addColumn('is_active', function ($occasion) {
                    return $occasion->is_active;
                })
                ->addColumn('created_at', function ($occasion) {
                    return $occasion->created_at_human;
                })
                ->rawColumns(['name'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('catalogmanagement::occasion.error_loading_data'),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new occasion
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $vendors = \Modules\Vendor\app\Models\Vendor::all();

        $data = [
            'title' => trans('catalogmanagement::occasion.add_occasion'),
            'languages' => $languages,
            'vendors' => $vendors,
        ];
        return view('catalogmanagement::occasions.form', $data);
    }

    /**
     * Store a newly created occasion
     */
    public function store(OccasionRequest $request)
    {
        try {
            $validated = $request->validated();
            $occasion = $this->occasionService->createOccasion($validated);

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('catalogmanagement::occasion.occasion_created'),
                    'data' => $occasion
                ]);
            }

            return redirect()->route('admin.occasions.index')
                ->with('success', trans('catalogmanagement::occasion.occasion_created'));
        } catch (\Exception $e) {
            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('catalogmanagement::occasion.error_creating_occasion') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', trans('catalogmanagement::occasion.error_creating_occasion') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified occasion
     */
    public function show($id)
    {
        $occasion = $this->occasionService->getOccasionById($id);
        $languages = $this->languageService->getAll();

        $data = [
            'title' => trans('catalogmanagement::occasion.view_occasion'),
            'occasion' => $occasion,
            'languages' => $languages,
        ];
        return view('catalogmanagement::occasions.show', $data);
    }

    /**
     * Show the form for editing the specified occasion
     */
    public function edit($id)
    {
        $occasion = $this->occasionService->getOccasionById($id);
        $languages = $this->languageService->getAll();
        $vendors = \Modules\Vendor\app\Models\Vendor::all();

        $data = [
            'title' => trans('catalogmanagement::occasion.edit_occasion'),
            'occasion' => $occasion,
            'languages' => $languages,
            'vendors' => $vendors,
        ];
        return view('catalogmanagement::occasions.form', $data);
    }

    /**
     * Update the specified occasion
     */
    public function update(OccasionRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $occasion = $this->occasionService->updateOccasion($id, $validated);

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('catalogmanagement::occasion.occasion_updated'),
                    'data' => $occasion
                ]);
            }

            return redirect()->route('admin.occasions.index')
                ->with('success', trans('catalogmanagement::occasion.occasion_updated'));
        } catch (\Exception $e) {
            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('catalogmanagement::occasion.error_updating_occasion') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', trans('catalogmanagement::occasion.error_updating_occasion') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified occasion
     */
    public function destroy(Request $request, $id)
    {
        try {
            $this->occasionService->deleteOccasion($id);

            return response()->json([
                'status' => true,
                'message' => trans('catalogmanagement::occasion.occasion_deleted'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('catalogmanagement::occasion.error_deleting_occasion') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search products for Select2 AJAX
     */
    public function searchProducts(Request $request)
    {
        try {
            $vendorId = $request->input('vendor_id');
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $perPage = 20;

            if (!$vendorId) {
                return response()->json([
                    'results' => [],
                    'pagination' => ['more' => false]
                ]);
            }

            // Query vendor product variants
            $query = \Modules\Vendor\app\Models\VendorProductVariant::query()
                ->where('vendor_id', $vendorId)
                ->with(['product', 'variantConfiguration'])
                ->when($search, function ($q) use ($search) {
                    $q->whereHas('product', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('sku', 'like', "%{$search}%");
                    });
                });

            $total = $query->count();
            $variants = $query->skip(($page - 1) * $perPage)
                             ->take($perPage)
                             ->get();

            $results = $variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'text' => $variant->product->name . ' - ' . ($variant->variantConfiguration->name ?? 'Default'),
                    'product_name' => $variant->product->name ?? 'N/A',
                    'variant_name' => $variant->variantConfiguration->name ?? 'Default',
                    'sku' => $variant->sku ?? 'N/A',
                    'original_price' => number_format($variant->price ?? 0, 2),
                    'special_price' => $variant->special_price ? number_format($variant->special_price, 2) : null,
                    'image' => $variant->product->main_image ? asset('storage/' . $variant->product->main_image) : null,
                ];
            });

            return response()->json([
                'results' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $total
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle occasion status
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'is_active' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $occasion = $this->occasionService->toggleOccasionStatus($id);

            return response()->json([
                'status' => true,
                'message' => trans('catalogmanagement::occasion.status_changed_successfully'),
                'data' => $occasion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('catalogmanagement::occasion.error_changing_status') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}
