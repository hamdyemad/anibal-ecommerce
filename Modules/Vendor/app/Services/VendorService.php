<?php

namespace Modules\Vendor\app\Services;

use Modules\Vendor\app\Interfaces\VendorInterface;

class VendorService
{
    public function __construct(public VendorInterface $vendorInterface)
    {
    }

    public function getAllVendors(array $filters = [], int $perPage = 10)
    {
        return $this->vendorInterface->getAllVendors($filters, $perPage);
    }

    public function getVendorById(int $id)
    {
        return $this->vendorInterface->getVendorById($id);
    }

    public function createVendor(array $data)
    {
        return $this->vendorInterface->createVendor($data);
    }

    public function updateVendor(int $id, array $data)
    {
        return $this->vendorInterface->updateVendor($id, $data);
    }

    public function deleteVendor(int $id)
    {
        return $this->vendorInterface->deleteVendor($id);
    }
}
