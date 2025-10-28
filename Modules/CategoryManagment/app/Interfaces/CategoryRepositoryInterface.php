<?php

namespace Modules\CategoryManagment\app\Interfaces;

interface CategoryRepositoryInterface
{
    /**
     * Get all categories with filters and pagination
     */
    public function getAllCategories(array $filters = [], int $perPage = 15);

    /**
     * Get categories query for DataTables
     */
    public function getCategoriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc');

    /**
     * Get all active categories
     */
    public function getActiveCategories();

    /**
     * Find category by ID
     */
    public function findById(int $id);

    /**
     * Create a new category
     */
    public function createCategory(array $data);

    /**
     * Update category
     */
    public function updateCategory(int $id, array $data);

    /**
     * Delete category
     */
    public function deleteCategory(int $id);

    /**
     * Get categories by department
     */
    public function getCategoriesByDepartment(int $departmentId);
}
