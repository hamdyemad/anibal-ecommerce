<?php

namespace Modules\Refund\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Refund\app\Services\RefundRequestService;
use Modules\Refund\app\Http\Requests\Api\StoreRefundRequestRequest;
use Modules\Refund\app\Http\Requests\Api\UpdateRefundStatusRequest;
use Modules\Refund\app\Http\Resources\RefundRequestResource;
use Modules\Refund\app\Http\Resources\RefundRequestCollection;

class RefundRequestApiController extends Controller
{
    use Res;
    protected $refundService;

    public function __construct(RefundRequestService $refundService)
    {
        $this->refundService = $refundService;
    }

    /**
     * Display a listing of refund requests
     */
    public function index(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'customer_id' => $request->get('customer_id'),
            'vendor_id' => $request->get('vendor_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'search' => $request->get('search'),
            'show_parent' => $request->get('show_parent'), // For customer view
        ];
        $perPage = $request->get('per_page', 12);
        $refunds = $this->refundService->getAllRefunds($filters, $perPage);
        return $this->sendRes(
            config('responses.refund_requests_retrieved_successfully')[app()->getLocale()],
            true,
            \Modules\Refund\app\Http\Resources\RefundRequestResource::collection($refunds)
        );
    }

    /**
     * Display the specified refund request
     */
    public function show($id)
    {
        $refund = $this->refundService->getRefundById($id);

        // Check authorization
        if (!$this->refundService->canUserAccessRefund($id, auth()->user())) {
            return $this->sendRes('Unauthorized', false, [], [], 403);
        }
        return $this->sendRes(
            config('responses.refund_request_retrieved_successfully')[app()->getLocale()],
            true,
            new RefundRequestResource($refund)
        );
    }

    /**
     * Create a new refund request
     */
    public function store(StoreRefundRequestRequest $request)
    {
        $refunds = $this->refundService->createRefund(
            $request->validated(),
            auth()->user()
        );

        return $this->sendRes(
            config('responses.refund_request_created_successfully')[app()->getLocale()],
            true,
            [],
            [],
            201
        );
    }

    /**
     * Cancel refund request (customer only)
     */
    public function cancel($id)
    {
        $refund = $this->refundService->cancelRefund($id, auth()->user());
        return $this->sendRes(
            config('responses.refund_request_cancelled_successfully')[app()->getLocale()],
            true,
            new RefundRequestResource($refund)
        );
    }

    /**
     * Get refund statistics
     */
    public function statistics(Request $request)
    {
        $filters = [
            'customer_id' => $request->get('customer_id'),
            'vendor_id' => $request->get('vendor_id'),
        ];
        $statistics = $this->refundService->getStatistics($filters);
        return $this->sendRes(
            config('responses.statistics_retrieved_successfully')[app()->getLocale()], // Using same key as list or new one
            true,
            $statistics
        );
    }
}
