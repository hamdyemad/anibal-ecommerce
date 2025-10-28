<?php

namespace Modules\Vendor\app\Interfaces;

interface VendorInterface
{
    public function getAllVendors(array $filters = [], int $perPage = 10);
    public function getVendorById(int $id);
    public function createVendor(array $data);
    public function updateVendor(int $id, array $data);
    public function deleteVendor(int $id);
}
