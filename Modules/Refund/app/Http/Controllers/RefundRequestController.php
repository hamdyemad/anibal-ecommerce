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
        // Get vendor ID if user is vendor
        $vendorId = null;
        if (!isAdmin()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            $vendorId = $vendor?->id;
        }
        
        // Get refund statistics
        $statistics = $this->refundService->getRefundStatistics($vendorId);
        
        return view('refund::refund-requests.index', compact('statistics'));
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
    public function approve(Request $request, $lang, $countryCode, $id)
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
                
                $refundRequestModel = $this->refundService->getRefundById($id);
                if ($refundRequestModel->vendor_id != $vendor->id) {
                    abort(403, 'Unauthorized');
                }
            }
            
            $this->refundService->approveRefund($id);
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('refund::refund.messages.approved_successfully'),
                ]);
            }
            
            return back()->with('success', trans('refund::refund.messages.approved_successfully'));
            
        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
            
            return back()->with('error', $e->getMessage());
        }
    }
    
    /**
     * Cancel refund request
     */
    public function cancel(Request $request, $lang, $countryCode, $id)
    {
        \Log::info('=== CANCEL REFUND START ===');
        \Log::info('Received ID parameter: ' . var_export($id, true));
        \Log::info('Request data: ' . json_encode($request->all()));
        
        try {
            // Validate the request
            $validated = $request->validate([
                'cancellation_reason' => 'required|string|max:1000',
            ], [
                'cancellation_reason.required' => trans('refund::refund.validation.cancellation_reason_required'),
                'cancellation_reason.max' => trans('validation.max.string', ['attribute' => trans('refund::refund.fields.cancellation_reason'), 'max' => 1000]),
            ]);
            
            \Log::info('Validation passed');
            
            // Convert ID to integer
            $id = (int) $id;
            
            \Log::info('Converted ID to integer: ' . $id);
            
            // Check if vendor can cancel this refund request
            if (!isAdmin()) {
                \Log::info('User is not admin, checking vendor access');
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if (!$vendor) {
                    \Log::error('No vendor found for user');
                    abort(403, 'Unauthorized');
                }
                
                \Log::info('Getting refund by ID: ' . $id);
                $refundRequestModel = $this->refundService->getRefundById($id);
                \Log::info('Refund found: ' . $refundRequestModel->id);
                
                if ($refundRequestModel->vendor_id != $vendor->id) {
                    \Log::error('Vendor ID mismatch');
                    abort(403, 'Unauthorized');
                }
            }
            
            \Log::info('Calling cancelRefund service method');
            $this->refundService->cancelRefund($id, $validated['cancellation_reason']);
            \Log::info('Cancel refund completed successfully');
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('refund::refund.messages.cancelled_successfully'),
                ]);
            }
            
            return back()->with('success', trans('refund::refund.messages.cancelled_successfully'));
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation exception: ' . $e->getMessage());
            \Log::error('Validation errors: ' . json_encode($e->errors()));
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('=== CANCEL REFUND ERROR ===');
            \Log::error('Error message: ' . $e->getMessage());
            \Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
            
            return back()->with('error', $e->getMessage());
        }
    }
    
    /**
     * Mark refund as in progress
     */
    public function markAsInProgress(Request $request, $lang, $countryCode, $id)
    {
        try {
            // Convert ID to integer
            $id = (int) $id;
            
            // Check if vendor can update this refund request
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
            
            $this->refundService->updateRefundStatus($id, ['status' => 'in_progress'], auth()->user());
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('refund::refund.messages.status_updated'),
                ]);
            }
            
            return back()->with('success', trans('refund::refund.messages.status_updated'));
            
        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
            
            return back()->with('error', $e->getMessage());
        }
    }
    
    /**
     * Mark refund as picked up
     */
    public function markAsPickedUp(Request $request, $lang, $countryCode, $id)
    {
        try {
            // Convert ID to integer
            $id = (int) $id;
            
            // Check if vendor can update this refund request
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
            
            $this->refundService->updateRefundStatus($id, ['status' => 'picked_up'], auth()->user());
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('refund::refund.messages.status_updated'),
                ]);
            }
            
            return back()->with('success', trans('refund::refund.messages.status_updated'));
            
        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
            
            return back()->with('error', $e->getMessage());
        }
    }
    
    /**
     * Mark refund as refunded
     */
    public function markAsRefunded(Request $request, $lang, $countryCode, $id)
    {
        try {
            // Convert ID to integer
            $id = (int) $id;
            
            // Check if vendor can update this refund request
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
            
            $this->refundService->updateRefundStatus($id, ['status' => 'refunded', 'refunded_at' => now()], auth()->user());
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('refund::refund.messages.refunded_successfully'),
                ]);
            }
            
            return back()->with('success', trans('refund::refund.messages.refunded_successfully'));
            
        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
            
            return back()->with('error', $e->getMessage());
        }
    }
    
    /**
     * Update vendor notes
     */
    public function updateNotes(UpdateRefundNotesRequest $request, $lang, $countryCode, $id)
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
