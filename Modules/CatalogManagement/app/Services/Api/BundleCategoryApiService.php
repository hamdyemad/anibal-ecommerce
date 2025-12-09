<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\Interfaces\Api\BundleCategoryRepositoryInterface;

class BundleCategoryApiService
{
    protected $bundleCategoryRepository;

    public function __construct(
        BundleCategoryRepositoryInterface $bundleCategoryRepository
    ) {
        $this->bundleCategoryRepository = $bundleCategoryRepository;
    }

    /**
     * Get all bundle categories with optional filters
     */
    public function getBundleCategoriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'desc')
    {
        return $this->bundleCategoryRepository->getBundleCategoriesQuery($filters, $orderBy, $orderDirection);
    }

    /**
     * Get all bundle categories with pagination
     */
    public function getAll(array $filters = [], $per_page = 10)
    {
        return $this->bundleCategoryRepository->getAll($filters, $per_page);
    }

    /**
     * Get bundle category by ID
     */
    public function getBundleCategoryById($id)
    {
        return $this->bundleCategoryRepository->getBundleCategoryById($id);
    }

    /**
     * Create bundle category
     */
    public function createBundleCategory(array $data)
    {
        return $this->bundleCategoryRepository->createBundleCategory($data);
    }

    /**
     * Update bundle category
     */
    public function updateBundleCategory($id, array $data)
    {
        return $this->bundleCategoryRepository->updateBundleCategory($id, $data);
    }

    /**
     * Delete bundle category
     */
    public function deleteBundleCategory($id)
    {
        return $this->bundleCategoryRepository->deleteBundleCategory($id);
    }

    /**
     * Get all active bundle categories
     */
    public function getActiveBundleCategories()
    {
        return $this->bundleCategoryRepository->getActiveBundleCategories();
    }

    /**
     * Toggle bundle category status
     */
    public function toggleBundleCategoryStatus($id)
    {
        return $this->bundleCategoryRepository->toggleBundleCategoryStatus($id);
    }
}
