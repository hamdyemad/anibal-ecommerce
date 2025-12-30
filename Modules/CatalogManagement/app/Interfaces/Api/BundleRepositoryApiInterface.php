<?php

namespace Modules\CatalogManagement\app\Interfaces\Api;

interface BundleRepositoryApiInterface
{
    /**
     * Get all bundles with filters
     */
    public function getAllBundles($filters = [], $perPage = 15);

    /**
     * Get bundle by ID
     */
    public function getBundleById($id, array $filters = []);

}
