<?php

namespace Modules\Order\app\Interfaces;

use Modules\Order\app\DTOs\ShippingFilterDTO;

interface ShippingRepositoryInterface
{
    /**
     * Get all shippings with filters
     */
    public function getAllShippings(array $data);

    /**
     * Get shipping by ID
     */
    public function getShippingById($id);

    /**
     * Create a new shipping
     */
    public function createShipping(array $data);

    /**
     * Update shipping
     */
    public function updateShipping($id, array $data);

    /**
     * Delete shipping
     */
    public function deleteShipping($id);

    /**
     * Change shipping status
     */
    public function changeStatus($id, $status);
}
