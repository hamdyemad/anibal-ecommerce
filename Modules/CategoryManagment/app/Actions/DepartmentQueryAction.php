<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\Department;

class DepartmentQueryAction
{
    public function handle(array $filters = [])
    {
        $query = Department::query()
                    ->with('translations')
                    ->withCount('activeVendorProducts as active_products_count')  // Count vendor products, not unique products
                    ->withCount('activeCategories')
                    ->active()
                    ->where('view_status', 1)
                    ->filter($filters)
                    ->orderBy('sort_number', 'asc');
        return $query;
    }
}
