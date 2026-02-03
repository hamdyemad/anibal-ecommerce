<?php

namespace Modules\Order\app\Services;

use Modules\Order\app\Repositories\OrderStageRepository;

class OrderStageService
{
    protected $orderStageRepository;

    public function __construct(OrderStageRepository $orderStageRepository)
    {
        $this->orderStageRepository = $orderStageRepository;
    }

    public function getOrderStagesQuery(array $filters = [], $orderBy = null, $orderDirection = 'desc')
    {
        return $this->orderStageRepository->getOrderStagesQuery($filters, $orderBy, $orderDirection);
    }

    public function getOrderStagesCount(array $filters = [])
    {
        return $this->orderStageRepository->getOrderStagesCount($filters);
    }

    public function getOrderStageById($id)
    {
        return $this->orderStageRepository->getOrderStageById($id);
    }

    public function createOrderStage(array $data)
    {
        return $this->orderStageRepository->createOrderStage($data);
    }

    public function updateOrderStage($id, array $data)
    {
        return $this->orderStageRepository->updateOrderStage($id, $data);
    }

    public function deleteOrderStage($id)
    {
        return $this->orderStageRepository->deleteOrderStage($id);
    }

    public function getActiveOrderStages()
    {
        return $this->orderStageRepository->getActiveOrderStages();
    }

    public function toggleOrderStageStatus($id)
    {
        return $this->orderStageRepository->toggleOrderStageStatus($id);
    }
}
