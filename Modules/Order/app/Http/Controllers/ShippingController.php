<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Order\app\Http\Requests\ShippingRequest;
use Modules\Order\app\Http\Resources\ShippingResource;
use Modules\Order\app\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(
        protected ShippingService $shippingService
    )
    {}

    /**
     * Display a listing of shippings (datatable)
     */
    public function index($lang, $countryCode)
    {
        return view('order::shippings.index');
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable($lang, $countryCode, Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'active' => $request->input('active'),
            'created_date_from' => $request->input('created_date_from'),
            'created_date_to' => $request->input('created_date_to'),
        ];

        $shippings = $this->shippingService->getAllShippings($filters);
        
        // Format data with index for DataTables using Resource
        $data = [];
        $startIndex = ($shippings->currentPage() - 1) * $shippings->perPage() + 1;
        foreach ($shippings->items() as $index => $shipping) {
            $shippingData = ShippingResource::make($shipping)->toArray($request);
            $shippingData['index'] = $startIndex + $index;
            $data[] = $shippingData;
        }

        return response()->json([
            'draw' => $request->get('draw', 1),
            'recordsTotal' => $shippings->total(),
            'recordsFiltered' => $shippings->total(),
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new shipping
     */
    public function create($lang, $countryCode)
    {
        $languages = \App\Models\Language::all();
        return view('order::shippings.form', compact('languages'));
    }

    /**
     * Store a newly created shipping in storage
     */
    public function store($lang, $countryCode, ShippingRequest $request)
    {
        try {
            $shipping = $this->shippingService->createShipping($request->validated());
            
            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('shipping.created_successfully'),
                    'redirect' => route('admin.shippings.index'),
                    'data' => $shipping
                ], 201);
            }
            
            return redirect()->route('admin.shippings.index')
                           ->with('success', trans('shipping.created_successfully'));
        } catch (\Exception $e) {
            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('shipping.error_creating'),
                    'errors' => []
                ], 500);
            }
            
            return back()->with('error', trans('shipping.error_creating'));
        }
    }

    /**
     * Display the specified shipping
     */
    public function show($lang, $countryCode, $id   )
    {
        $shipping = $this->shippingService->getShippingById($id);
        return view('order::shippings.show', compact('shipping'));
    }

    /**
     * Show the form for editing the specified shipping
     */
    public function edit($lang, $countryCode, $id)
    {
        $shipping = $this->shippingService->getShippingById($id);
        $languages = \App\Models\Language::all();
        return view('order::shippings.form', compact('shipping', 'languages'));
    }

    /**
     * Update the specified shipping in storage
     */
    public function update($lang, $countryCode, $id, ShippingRequest $request)
    {
        try {
            $shipping = $this->shippingService->updateShipping($id, $request->validated());
            
            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('shipping.updated_successfully'),
                    'redirect' => route('admin.shippings.index'),
                    'data' => $shipping
                ], 200);
            }
            
            return redirect()->route('admin.shippings.index')
                           ->with('success', trans('shipping.updated_successfully'));
        } catch (\Exception $e) {
            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('shipping.error_updating'),
                    'errors' => []
                ], 500);
            }
            
            return back()->with('error', trans('shipping.error_updating'));
        }
    }

    /**
     * Remove the specified shipping from storage
     */
    public function destroy($lang, $countryCode, $id)
    {
        try {
            $this->shippingService->deleteShipping($id);
            return redirect()->route('admin.shippings.index')
                           ->with('success', trans('shipping.deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', trans('shipping.error_deleting'));
        }
    }

    /**
     * Change shipping status
     */
    public function changeStatus($lang, $countryCode, $id, Request $request)
    {
        try {
            $status = $request->input('status');
            $this->shippingService->changeStatus($id, $status);
            return response()->json([
                'status' => true,
                'message' => trans('shipping.status_changed_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('shipping.error_changing_status')
            ], 500);
        }
    }
}
