<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\Interfaces\Api\BundleCategoryApiRepositoryInterface;

class BundleCategoryApiService
{
    protected $bundleCategoryRepository;

    public function __construct(
        BundleCategoryApiRepositoryInterface $bundleCategoryRepository
    ) {
        $this->bundleCategoryRepository = $bundleCategoryRepository;
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

}
