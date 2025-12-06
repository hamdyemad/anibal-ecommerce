<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Modules\Order\app\Services\Api\OrderStageApiService;
use Modules\Order\app\Http\Resources\Api\OrderStageResource;

class OrderStageApiController extends Controller
{
    use Res;

    protected $orderStageApiService;

    public function __construct(OrderStageApiService $orderStageApiService)
    {
        $this->orderStageApiService = $orderStageApiService;
    }

    /**
     * Get all active order stages
     */
    public function index()
    {
        $stages = $this->orderStageApiService->getActiveOrderStages();
        $message = config('responses.order_stages_retrieved_successfully.' . app()->getLocale());

        return $this->sendRes($message, true, OrderStageResource::collection($stages), [], 200);
    }

    /**
     * Get order stage by ID
     */
    public function show($id)
    {
        $stage = $this->orderStageApiService->getOrderStageById($id);
        $message = config('responses.order_stage_retrieved_successfully.' . app()->getLocale());

        return $this->sendRes($message, true, new OrderStageResource($stage), [], 200);
    }

    /**
     * Get allowed stage transitions for an order
     */
    public function allowedStages($orderId)
    {
        $stages = $this->orderStageApiService->getAllowedStagesForOrder($orderId);
        $message = config('responses.order_stages_retrieved_successfully.' . app()->getLocale());

        return $this->sendRes($message, true, OrderStageResource::collection($stages), [], 200);
    }
}
