<?php

namespace Modules\Refund\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Refund\app\Models\VendorRefundSetting;

class VendorRefundSettingController extends Controller
{
    /**
     * Display vendor refund settings
     */
    public function index()
    {
        // If admin, redirect to admin settings page
        if (isAdmin()) {
            return redirect()->route('admin.refunds.admin-settings.index');
        }

        $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
        
        if (!$vendor) {
            abort(403, 'Unauthorized - No vendor associated with this user');
        }

        $vendorSettings = VendorRefundSetting::getForVendor($vendor->id);
        
        return view('refund::vendor-settings.index', compact('vendorSettings', 'vendor'));
    }
    
    /**
     * Update vendor refund settings
     */
    public function update(Request $request)
    {
        // Admin should use admin settings route
        if (isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Please use admin settings page',
            ], 403);
        }

        $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
        
        if (!$vendor) {
            abort(403, 'Unauthorized - No vendor associated with this user');
        }

        $validated = $request->validate([
            'refund_processing_days' => 'required|integer|min:1|max:365',
            'customer_pays_return_shipping' => 'required|boolean',
        ], [
            'refund_processing_days.required' => trans('refund::refund.validation.refund_processing_days_required'),
            'refund_processing_days.integer' => trans('refund::refund.validation.refund_processing_days_integer'),
            'refund_processing_days.min' => trans('refund::refund.validation.refund_processing_days_min', ['min' => 1]),
            'refund_processing_days.max' => trans('refund::refund.validation.refund_processing_days_max', ['max' => 365]),
            'customer_pays_return_shipping.boolean' => trans('refund::refund.validation.customer_pays_return_shipping_boolean'),
        ]);

        $vendorSettings = VendorRefundSetting::getForVendor($vendor->id);
        $vendorSettings->update($validated);
        
        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => trans('refund::refund.messages.settings_updated'),
            ]);
        }
        
        return back()->with('success', trans('refund::refund.messages.settings_updated'));
    }
}
