<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\Interfaces\Api\BundleRepositoryApiInterface;

class BundleApiService
{
    protected $bundleRepository;

    public function __construct(BundleRepositoryApiInterface $bundleRepository)
    {
        $this->bundleRepository = $bundleRepository;
    }

    /**
     * Get all bundles
     */
    public function getAllBundles($filters = [], $perPage = 15)
    {
        return $this->bundleRepository->getAllBundles($filters, $perPage);
    }

    /**
     * Get bundle by ID
     */
    public function getBundleById($id)
    {
        return $this->bundleRepository->getBundleById($id);
    }

}
