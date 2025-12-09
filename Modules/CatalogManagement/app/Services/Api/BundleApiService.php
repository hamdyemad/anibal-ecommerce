<?php

namespace Modules\CatalogManagement\app\Services\Api;


class BundleApiService
{
    protected $bundleRepository;

    public function __construct(BundleRepos $bundleRepository)
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

    /**
     * Create bundle
     */
    public function createBundle($data)
    {
        return $this->bundleRepository->createBundle($data);
    }

    /**
     * Update bundle
     */
    public function updateBundle($bundle, $data)
    {
        return $this->bundleRepository->updateBundle($bundle, $data);
    }

    /**
     * Delete bundle
     */
    public function deleteBundle($bundle)
    {
        return $this->bundleRepository->deleteBundle($bundle);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($bundle)
    {
        return $this->bundleRepository->toggleActive($bundle);
    }
}
