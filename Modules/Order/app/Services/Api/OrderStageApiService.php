<?php

namespace Modules\Order\app\Services\Api;

use Modules\Order\app\Interfaces\Api\OrderStageApiRepositoryInterface;

class OrderStageApiService
{
    protected $orderStageApiRepository;

    public function __construct(OrderStageApiRepositoryInterface $orderStageApiRepository)
    {
        $this->orderStageApiRepository = $orderStageApiRepository;
    }

    /**
     * Get all active order stages
     */
    public function getActiveOrderStages()
    {
        return $this->orderStageApiRepository->getActiveOrderStages();
    }

    /**
     * Get order stage by ID
     */
    public function getOrderStageById($id)
    {
        return $this->orderStageApiRepository->getOrderStageById($id);
    }

    /**
     * Get allowed transitions for an order
     */
    public function getAllowedStagesForOrder($orderId)
    {
        return $this->orderStageApiRepository->getAllowedStagesForOrder($orderId);
    }
}
