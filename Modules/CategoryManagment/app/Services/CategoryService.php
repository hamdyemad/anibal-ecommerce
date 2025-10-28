<?php

namespace Modules\CategoryManagment\app\Services;

use Modules\CategoryManagment\app\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories with filters and pagination
     */
    public function getAllCategories(array $filters = [], int $perPage = 15)
    {
        try {
            return $this->categoryRepository->getAllCategories($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get categories query for DataTables
     */
    public function getCategoriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->categoryRepository->getCategoriesQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching categories query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active categories
     */
    public function getActiveCategories()
    {
        try {
            return $this->categoryRepository->getActiveCategories();
        } catch (\Exception $e) {
            Log::error('Error fetching active categories: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get category by ID
     */
    public function getCategoryById(int $id)
    {
        try {
            return $this->categoryRepository->findById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching category: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data)
    {
        try {
            return $this->categoryRepository->createCategory($data);
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update category
     */
    public function updateCategory(int $id, array $data)
    {
        try {
            return $this->categoryRepository->updateCategory($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete category
     */
    public function deleteCategory(int $id)
    {
        try {
            return $this->categoryRepository->deleteCategory($id);
        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get categories by department
     */
    public function getCategoriesByDepartment(int $departmentId)
    {
        try {
            return $this->categoryRepository->getCategoriesByDepartment($departmentId);
        } catch (\Exception $e) {
            Log::error('Error fetching categories by department: ' . $e->getMessage());
            throw $e;
        }
    }
}
