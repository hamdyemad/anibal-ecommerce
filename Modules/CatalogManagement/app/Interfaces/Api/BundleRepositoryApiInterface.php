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
    public function getBundleById($id);

    /**
     * Create bundle
     */
    public function createBundle($data);

    /**
     * Update bundle
     */
    public function updateBundle($bundle, $data);

    /**
     * Delete bundle
     */
    public function deleteBundle($bundle);

    /**
     * Store translations
     */
    public function storeTranslations($bundle, $data);

    /**
     * Toggle active status
     */
    public function toggleActive($bundle);
}
