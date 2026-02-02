<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\Category;

class CategoryQueryAction
{
    public function handle(array $filters = [])
    {
        $sortBy = $filters['sort'] ?? 'sort_number';
        $sortType = $filters['sort_type'] ?? 'asc';
        
        // Determine subcategory sort
        $subCategorySort = 'sort_number';
        $subCategorySortType = 'asc';
        
        if ($sortBy === 'sub_categories_products') {
            $subCategorySort = 'active_products_count';
            $subCategorySortType = $sortType;
        }
        
        $query = Category::query()
                    ->active()
                    ->where('view_status', 1)
                    ->withCount('activeSubs')
                    ->withCount(['activeVendorProducts as active_products_count'])  // Count vendor products
                    ->with(['department' => function($q) {
                        $q->withCount(['activeVendorProducts as active_products_count']);  // Count vendor products
                    }, 'translations',
                    ])
                    ->filter($filters)
                    ->orderBy('sort_number', $subCategorySortType);
        return $query;
    }
}
