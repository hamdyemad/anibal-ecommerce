<?php

namespace Modules\CategoryManagment\app\Actions;

use App\Models\Attachment;
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
                    // Add image and icon paths as subqueries to avoid N+1
                    ->addSelect([
                        'image_path' => Attachment::selectRaw('path')
                            ->whereColumn('attachable_id', 'categories.id')
                            ->where('attachable_type', Category::class)
                            ->where('type', 'image')
                            ->limit(1),
                        'icon_path' => Attachment::selectRaw('path')
                            ->whereColumn('attachable_id', 'categories.id')
                            ->where('attachable_type', Category::class)
                            ->where('type', 'icon')
                            ->limit(1)
                    ])
                    ->with([
                        'department' => function($q) {
                            $q->withCount(['activeVendorProducts as active_products_count'])
                              ->with('translations')
                              // Add department image and icon as subqueries
                              ->addSelect([
                                  'image_path' => \App\Models\Attachment::selectRaw('path')
                                      ->whereColumn('attachable_id', 'departments.id')
                                      ->where('attachable_type', \Modules\CategoryManagment\app\Models\Department::class)
                                      ->where('type', 'image')
                                      ->limit(1),
                                  'icon_path' => \App\Models\Attachment::selectRaw('path')
                                      ->whereColumn('attachable_id', 'departments.id')
                                      ->where('attachable_type', \Modules\CategoryManagment\app\Models\Department::class)
                                      ->where('type', 'icon')
                                      ->limit(1)
                              ]);
                        },
                        'translations',
                        // Removed activeSubs eager loading - not needed in API response
                    ])
                    ->filter($filters)
                    ->orderBy('sort_number', $subCategorySortType);
        return $query;
    }
}
