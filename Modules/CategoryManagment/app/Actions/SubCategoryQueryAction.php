<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\SubCategory;

class SubCategoryQueryAction
{
    public function handle(array $filters = [])
    {
        $query = SubCategory::query()
                    ->active()
                    ->where('view_status', 1)
                    ->withCount('activeProducts')
                    ->with(['category','category.department', 'translations'])
                    ->filter($filters)
                    ->orderBy('sort_number', 'asc');
        return $query;
    }
}
