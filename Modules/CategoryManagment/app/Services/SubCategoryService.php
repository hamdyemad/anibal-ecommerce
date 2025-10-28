<?php

namespace Modules\CategoryManagment\app\Services;

use Modules\CategoryManagment\app\Interfaces\SubCategoryRepositoryInterface;
use Illuminate\Support\Facades\Log;

class SubCategoryService
{
    protected $subCategoryRepository;

    public function __construct(SubCategoryRepositoryInterface $subCategoryRepository)
    {
        $this->subCategoryRepository = $subCategoryRepository;
    }

    /**
     * Get all sub-categories with filters and pagination
     */
    public function getAllSubCategories(array $filters = [], int $perPage = 15)
    {
        try {
            return $this->subCategoryRepository->getAllSubCategories($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching sub-categories: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get sub-categories query for DataTables
     */
    public function getSubCategoriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->subCategoryRepository->getSubCategoriesQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching sub-categories query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active sub-categories
     */
    public function getActiveSubCategories()
    {
        try {
            return $this->subCategoryRepository->getActiveSubCategories();
        } catch (\Exception $e) {
            Log::error('Error fetching active sub-categories: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get sub-category by ID
     */
    public function getSubCategoryById(int $id)
    {
        try {
            return $this->subCategoryRepository->findById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching sub-category: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new sub-category
     */
    public function createSubCategory(array $data)
    {
        try {
            return $this->subCategoryRepository->createSubCategory($data);
        } catch (\Exception $e) {
            Log::error('Error creating sub-category: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update sub-category
     */
    public function updateSubCategory(int $id, array $data)
    {
        try {
            return $this->subCategoryRepository->updateSubCategory($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating sub-category: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete sub-category
     */
    public function deleteSubCategory(int $id)
    {
        try {
            return $this->subCategoryRepository->deleteSubCategory($id);
        } catch (\Exception $e) {
            Log::error('Error deleting sub-category: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get sub-categories by category
     */
    public function getSubCategoriesByCategory(int $categoryId)
    {
        try {
            return $this->subCategoryRepository->getSubCategoriesByCategory($categoryId);
        } catch (\Exception $e) {
            Log::error('Error fetching sub-categories by category: ' . $e->getMessage());
            throw $e;
        }
    }
}
