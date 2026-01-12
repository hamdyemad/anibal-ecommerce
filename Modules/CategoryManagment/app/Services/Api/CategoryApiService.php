<?php

namespace Modules\CategoryManagment\app\Services\Api;

use Modules\CategoryManagment\app\DTOs\CategoryFilterDTO;
use Modules\CategoryManagment\app\DTOs\DepartmentFilterDTO;
use Modules\CategoryManagment\app\Interfaces\Api\CategoryApiRepositoryInterface;
use Modules\CategoryManagment\app\Interfaces\Api\DepartmentApiRepositoryInterface;
use Modules\CategoryManagment\app\Interfaces\Api\SubCategoryApiRepositoryInterface;

class CategoryApiService
{
    public function __construct(
        protected CategoryApiRepositoryInterface $CategoryRepository,
        protected DepartmentApiRepositoryInterface $DepartmentRepository,
        protected SubCategoryApiRepositoryInterface $SubCategoryRepository,
    ) {}

    /**
     * Get all categories with filters and pagination
     */
    public function getAllCategories(CategoryFilterDTO $dto)
    {
        return $this->CategoryRepository->getAllCategories($dto);
    }

    /**
     * Get Category by ID
     */
    public function find(CategoryFilterDTO $dto, $id)
    {
        return $this->CategoryRepository->find($dto, $id);
    }

    /**
     * Get categories by filters (handles department, category, and sub-category hierarchy)
     * Returns categories or sub-categories based on filter combination
     */
    public function getCategoriesByFilters(array $filters)
    {
        $result = [];
        
        // If sub_category_id is provided, return empty (no further children)
        if (!empty($filters['sub_category_id'])) {
            return [];
        }

        // If category_id is provided, return sub-categories
        $categoryId = $filters['main_category_id'] ?? $filters['category_id'] ?? null;
        if (!empty($categoryId)) {
            $subCategories = $this->SubCategoryRepository->getSubCategoriesByCategory($categoryId);
            
            // Format sub-categories
            foreach ($subCategories as $subCategory) {
                $result[] = [
                    'id' => $subCategory->id,
                    'title' => $subCategory->name,
                    'slug' => $subCategory->slug,
                    'image' => $subCategory->image ? asset('storage/' . $subCategory->image) : null,
                    'icon' => $subCategory->icon ? asset('storage/' . $subCategory->icon) : null,
                    'type' => 'sub_category',
                    'parent_id' => (int) $categoryId,
                ];
            }
            
            return $result;
        }
        
        // If department_id only is provided, return categories (without sub-categories)
        if (!empty($filters['department_id'])) {
            $dto = new CategoryFilterDTO(department_id: $filters['department_id']);
            $categories = $this->CategoryRepository->getAllCategories($dto);
            
            // Add categories only
            foreach ($categories as $category) {
                $result[] = [
                    'id' => $category->id,
                    'title' => $category->name,
                    'slug' => $category->slug,
                    'image' => $category->image ? asset('storage/' . $category->image) : null,
                    'icon' => $category->icon ? asset('storage/' . $category->icon) : null,
                    'type' => 'category',
                ];
            }
            
            return $result;
        }

        // If brand_id only, return all departments that have products from this brand
        if (!empty($filters['brand_id'])) {
            return $this->DepartmentRepository->getDepartmentsByBrand($filters['brand_id']);
        }

        // Return all active departments
        $dto = new DepartmentFilterDTO();
        return $this->DepartmentRepository->getAllDepartments($dto);
    }


    public function getCategoriesByIds(array $filters)
    {
        if (!empty($filters['main_category_id'])) {
            $dto = new CategoryFilterDTO();
            return $this->CategoryRepository->find($dto, $filters['main_category_id']);
        }

        if (!empty($filters['sub_category_id'])) {
            $dto = new CategoryFilterDTO();
            return $this->SubCategoryRepository->find($dto, $filters['sub_category_id']);
        }

        if (!empty($filters['department_id'])) {
            $dto = new DepartmentFilterDTO();
            return $this->DepartmentRepository->find($dto, $filters['department_id']);
        }

        // If brand_id only, return all departments that have products from this brand
        if (!empty($filters['brand_id'])) {
            return $this->DepartmentRepository->getDepartmentsByBrand($filters['brand_id']);
        }

        return [];
    }
}
