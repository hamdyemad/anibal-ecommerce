<?php

namespace Modules\Order\app\Interfaces\Api;

interface OrderStageApiRepositoryInterface
{
    /**
     * Get all active order stages
     */
    public function getActiveOrderStages();

    /**
     * Get order stage by ID
     */
    public function getOrderStageById($id);

    public function getAllowedStagesForOrder($orderId);
}
