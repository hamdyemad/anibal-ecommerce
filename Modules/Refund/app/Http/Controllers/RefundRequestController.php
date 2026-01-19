<?php

namespace Modules\Refund\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Refund\app\Services\RefundRequestService;
use Modules\Refund\app\DataTables\RefundRequestDataTable;
use Modules\Refund\app\Http\Requests\RejectRefundRequest;
use Modules\Refund\app\Http\Requests\ChangeRefundStatusRequest;
use Modules\Refund\app\Http\Requests\UpdateRefundNotesRequest;

class RefundRequestController extends Controller
{
    protected $refundService;

    public function __construct(RefundRequestService $refundService)
    {
        $this->refundService = $refundService;
    }

    /**
     * Display a listing of refund requests
     */
    public function index()
    {
        return view('refund::refund-requests.index');
    }
    
    /**
     * DataTable endpoint for refund requests
     */
    public function datatable(Request $request, RefundRequestDataTable $dataTable)
    {
        return response()->json($dataTable->handle($request));
    }
    
    /**
     * Display the specified refund request
     */
    public function show($lang, $countryCode, $id)
    {
        // Convert ID to integer
        $id = (int) $id;
        // Check if vendor can view this refund request
        if (!isAdmin()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if (!$vendor) {
                abort(403, 'Unauthorized');
            }
        }
        
        $refundRequest = $this->refundService->getRefundWithRelations($id, [
            'order', 
            'customer', 
            'vendor', 
            'items.orderProduct.vendorProduct.product',
            'history.user'
        ]);
        
        // Check vendor access
        if (!isAdmin()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($refundRequest->vendor_id != $vendor->id) {
                abort(403, 'Unauthorized');
            }
        }
        
        return view('refund::refund-requests.show', compact('refundRequest'));
    }
    
    /**
     * Approve refund request
     */
    public function approve($id)
    {
        try {
            // Convert ID to integer
            $id = (int) $id;
            
            // Check if vendor can approve this refund request
            if (!isAdmin()) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if (!$vendor) {
                    abort(403, 'Unauthorized');
                }
                
                $refundRequest = $this->refundService->getRefundById($id);
                if ($refundRequest->vendor_id != $vendor->id) {
                    abort(403, 'Unauthorized');
                }
            }
            
            $this->refundService->approveRefund($id);
            
            return back()->with('success', trans('refund::refund.messages.approved_successfully'));
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    /**
     * Reject refund request
     */
    public function reject(RejectRefundRequest $request, $id)
    {
        try {
            // Convert ID to integer
            $id = (int) $id;
            
            // Check if vendor can reject this refund request
            if (!isAdmin()) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if (!$vendor) {
                    abort(403, 'Unauthorized');
                }
                
                $refundRequest = $this->refundService->getRefundById($id);
                if ($refundRequest->vendor_id != $vendor->id) {
                    abort(403, 'Unauthorized');
                }
            }
            
            $this->refundService->rejectRefund($id, $request->rejection_reason);
            
            return back()->with('success', trans('refund::refund.messages.rejected_successfully'));
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    /**
     * Change refund request status
     */
    public function changeStatus(ChangeRefundStatusRequest $request, $id)
    {
        try {
            // Convert ID to integer
            $id = (int) $id;
            
            // Check if vendor can change status
            if (!isAdmin()) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if (!$vendor) {
                    abort(403, 'Unauthorized');
                }
                
                $refundRequest = $this->refundService->getRefundById($id);
                if ($refundRequest->vendor_id != $vendor->id) {
                    abort(403, 'Unauthorized');
                }
            }
            
            $this->refundService->updateRefundStatus($id, ['status' => $request->status], auth()->user());
            
            return back()->with('success', trans('refund::refund.messages.status_updated'));
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    /**
     * Update vendor notes
     */
    public function updateNotes(UpdateRefundNotesRequest $request, $id)
    {
        try {
            // Convert ID to integer
            $id = (int) $id;
            
            // Check if vendor can update notes
            if (!isAdmin()) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if (!$vendor) {
                    abort(403, 'Unauthorized');
                }
                
                $refundRequest = $this->refundService->getRefundById($id);
                if ($refundRequest->vendor_id != $vendor->id) {
                    abort(403, 'Unauthorized');
                }
            }
            
            $this->refundService->updateNotes($id, $request->notes, isAdmin());
            
            return back()->with('success', trans('refund::refund.messages.notes_updated'));
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
