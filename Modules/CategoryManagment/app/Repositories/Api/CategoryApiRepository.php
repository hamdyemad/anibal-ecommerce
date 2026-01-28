<?php

namespace Modules\CategoryManagment\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\CategoryManagment\app\Actions\CategoryQueryAction;
use Modules\CategoryManagment\app\DTOs\CategoryFilterDTO;
use Modules\CategoryManagment\app\Interfaces\Api\CategoryApiRepositoryInterface;

class CategoryApiRepository implements CategoryApiRepositoryInterface
{

    public function __construct(protected CategoryQueryAction $query, protected IsPaginatedAction $paginated){}
    /**
     * Get all Categories with filters and pagination
     */
    public function getAllCategories(CategoryFilterDTO $dto)
    {
        $filters = $dto->toArray();
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $dto->per_page, $dto->paginated);
        return $result;
    }


    /**
     * Get Category by ID
     */
    public function find(CategoryFilterDTO $dto, $id)
    {
        $filters = $dto->toArray();
        $sortBy = $filters['sort'] ?? 'sort_number';
        $sortType = $filters['sort_type'] ?? 'asc';
        
        // Determine subcategory sort
        $subCategorySort = 'sort_number';
        $subCategorySortType = 'asc';
        
        if ($sortBy === 'sub_categories_products') {
            $subCategorySort = 'active_products_count';
            $subCategorySortType = $sortType;
        }
        
        return $this->query->handle($filters)
            ->with([
                'department' => function($q) {
                    $q->withCount(['activeVendorProducts as active_products_count']);  // Count vendor products
                },
                'activeSubs' => function($q) use ($subCategorySort, $subCategorySortType) {
                    $q->withCount(['activeVendorProducts as active_products_count'])  // Count vendor products
                      ->orderBy($subCategorySort, $subCategorySortType);
                }
            ])
            ->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))
            ->firstOrFail();
    }

    /**
     * Get categories by department ID or slug
     */
    public function getCategoriesByDepartment($departmentId)
    {
        return $this->query->handle([])
            ->byDepartment($departmentId)
            ->with('department')
            ->get();
    }

}
