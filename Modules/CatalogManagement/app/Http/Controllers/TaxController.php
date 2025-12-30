<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\Services\TaxService;
use Modules\CatalogManagement\app\Http\Requests\TaxRequest;
use Modules\CatalogManagement\app\Actions\TaxAction;

class TaxController extends Controller
{

    public function __construct(
        protected TaxService $taxService,
        protected LanguageService $languageService,
        protected TaxAction $taxAction
    ) {
        $this->middleware('can:taxes.index')->only(['index', 'datatable']);
        $this->middleware('can:taxes.show')->only(['show']);
        $this->middleware('can:taxes.create')->only(['create', 'store']);
        $this->middleware('can:taxes.edit')->only(['edit', 'update', 'toggleStatus']);
        $this->middleware('can:taxes.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = $this->languageService->getAll();
        return view('catalogmanagement::tax.index', compact('languages'));
    }

    /**
     * Get taxes data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = $this->taxAction->getDatatableData($request);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        return view('catalogmanagement::tax.form', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaxRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->taxService->createTax($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('catalogmanagement::tax.tax_created'),
                    'redirect' => route('admin.taxes.index')
                ]);
            }

            return redirect()->route('admin.taxes.index')
                ->with('success', __('catalogmanagement::tax.tax_created'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::tax.error_creating_tax') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('catalogmanagement::tax.error_creating_tax') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $lang, string $countryCode, string $id)
    {
        try {
            $tax = $this->taxService->getTaxById($id);
            $languages = $this->languageService->getAll();
            return view('catalogmanagement::tax.view', compact('tax', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.taxes.index')
                ->with('error', __('catalogmanagement::tax.tax_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $lang, string $countryCode, string $id)
    {
        try {
            $tax = $this->taxService->getTaxById($id);
            $languages = $this->languageService->getAll();
            return view('catalogmanagement::tax.form', compact('tax', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.taxes.index')
                ->with('error', __('catalogmanagement::tax.tax_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaxRequest $request, string $lang, string $countryCode, string $id)
    {
        $validated = $request->validated();

        try {
            $this->taxService->updateTax($id, $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('catalogmanagement::tax.tax_updated'),
                    'redirect' => route('admin.taxes.index')
                ]);
            }

            return redirect()->route('admin.taxes.index')
                ->with('success', __('catalogmanagement::tax.tax_updated'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::tax.error_updating_tax') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('catalogmanagement::tax.error_updating_tax') . ': ' . $e->getMessage());
        }
    }

    /**
     * Toggle tax status
     */
    public function toggleStatus(Request $request, string $lang, string $countryCode, string $id)
    {
        try {
            $tax = $this->taxService->getTaxById($id);
            $this->taxService->updateTax($id, [
                'is_active' => $request->input('is_active', 0),
                'percentage' => $tax->percentage,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::tax.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::tax.error_updating_status'),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $lang, string $countryCode, string $id)
    {
        try {
            $this->taxService->deleteTax($id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('catalogmanagement::tax.tax_deleted'),
                    'redirect' => route('admin.taxes.index')
                ]);
            }

            return redirect()->route('admin.taxes.index')
                ->with('success', __('catalogmanagement::tax.tax_deleted'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::tax.error_deleting_tax') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.taxes.index')
                ->with('error', __('catalogmanagement::tax.error_deleting_tax') . ': ' . $e->getMessage());
        }
    }
}
