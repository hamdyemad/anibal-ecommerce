<?php

namespace Modules\Refund\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Refund\app\DataTables\VendorRefundSettingDataTable;
use Modules\Refund\app\Http\Requests\UpdateRefundDaysRequest;
use Modules\Refund\app\Http\Requests\UpdateCustomerPaysShippingRequest;
use Modules\Refund\app\Services\VendorRefundSettingService;

class AdminVendorRefundSettingController extends Controller
{
    public function __construct(
        protected VendorRefundSettingDataTable $vendorRefundSettingDataTable,
        protected VendorRefundSettingService $vendorRefundSettingService
    ) {
    }

    /**
     * Display all vendors' refund settings (Admin only)
     */
    public function index(Request $request)
    {
        if (!isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return view('refund::admin-settings.index');
    }

    /**
     * DataTable endpoint for vendors' refund settings
     */
    public function datatable(Request $request)
    {
        if (!isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'search' => $request->get('search'),
        ];

        $response = $this->vendorRefundSettingDataTable->getDataTable($data);

        return response()->json([
            'data' => $response['data'],
            'recordsTotal' => $response['totalRecords'],
            'recordsFiltered' => $response['filteredRecords'],
            'draw' => intval($request->draw ?? 1),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Update refund days for a vendor
     */
    public function updateRefundDays(UpdateRefundDaysRequest $request, $lang, $countryCode, $vendorId)
    {
        $this->vendorRefundSettingService->updateRefundDays(
            $vendorId, 
            $request->validated()['refund_processing_days']
        );

        return response()->json([
            'success' => true,
            'message' => trans('refund::refund.messages.settings_updated'),
        ]);
    }

    /**
     * Update customer pays shipping for a vendor
     */
    public function updateCustomerPaysShipping(UpdateCustomerPaysShippingRequest $request, $lang, $countryCode, $vendorId)
    {
        $this->vendorRefundSettingService->updateCustomerPaysShipping(
            $vendorId, 
            $request->validated()['customer_pays_return_shipping']
        );

        return response()->json([
            'success' => true,
            'message' => trans('refund::refund.messages.settings_updated'),
        ]);
    }
}
