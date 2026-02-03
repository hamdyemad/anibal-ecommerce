<?php

namespace Modules\Order\app\Interfaces;

interface OrderStageRepositoryInterface
{
    public function getOrderStagesQuery(array $filters = [], $orderBy = null, $orderDirection = 'desc');
    public function getOrderStagesCount(array $filters = []);
    public function getOrderStageById($id);
    public function createOrderStage(array $data);
    public function updateOrderStage($id, array $data);
    public function deleteOrderStage($id);
    public function getActiveOrderStages();
    public function toggleOrderStageStatus($id);
}
