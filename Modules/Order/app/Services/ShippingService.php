<?php

namespace Modules\Order\app\Services;

use Modules\Order\app\Interfaces\ShippingRepositoryInterface;

class ShippingService
{
    public function __construct(
        private ShippingRepositoryInterface $shippingRepository
    ) {}

    /**
     * Get all shippings with filters
     */
    public function getAllShippings(array $filters)
    {
        return $this->shippingRepository->getAllShippings($filters);
    }

    /**
     * Get shipping by ID
     */
    public function getShippingById($id)
    {
        return $this->shippingRepository->getShippingById($id);
    }

    /**
     * Create a new shipping
     */
    public function createShipping(array $data)
    {
        return $this->shippingRepository->createShipping($data);
    }

    /**
     * Update shipping
     */
    public function updateShipping($id, array $data)
    {
        return $this->shippingRepository->updateShipping($id, $data);
    }

    /**
     * Delete shipping
     */
    public function deleteShipping($id)
    {
        return $this->shippingRepository->deleteShipping($id);
    }

    /**
     * Change shipping status
     */
    public function changeStatus($id, $status)
    {
        return $this->shippingRepository->changeStatus($id, $status);
    }
}
