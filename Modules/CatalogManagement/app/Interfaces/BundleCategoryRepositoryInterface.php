<?php

namespace Modules\CatalogManagement\app\Interfaces;

interface BundleCategoryRepositoryInterface
{
    /**
     * Get all bundle categories with optional filters
     */
    public function getBundleCategoriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'desc');


    // Get All Bundle Categories per page
    public function getAll(array $filters = [], $per_page);


    /**
     * Get bundle category by ID
     */
    public function getBundleCategoryById($id);

    /**
     * Create new bundle category
     */
    public function createBundleCategory(array $data);

    /**
     * Update bundle category
     */
    public function updateBundleCategory($id, array $data);

    /**
     * Delete bundle category
     */
    public function deleteBundleCategory($id);

    /**
     * Get active bundle categories
     */
    public function getActiveBundleCategories();

    /**
     * Toggle bundle category status
     */
    public function toggleBundleCategoryStatus($id);
}
