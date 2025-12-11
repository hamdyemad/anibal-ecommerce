<?php

namespace Modules\CatalogManagement\app\Actions;

use Modules\CatalogManagement\app\Models\Brand;

class BrandQueryAction
{
    /**
     * Build the query with filters
     */
    public function handle(array $filters = [])
    {
        $query = Brand::query()->active();

        // Load relationships
        $query->with(['translations', 'logo', 'cover']);

        if (!empty($filters)) {
            $query->filter($filters);
        }

        $query = $this->applySorting($query, $filters);

        return $query;
    }


    protected function applySorting($query, array $filters)
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortType = $filters['sort_type'] ?? 'desc';

        if (!in_array($sortType, ['asc', 'desc'])) {
            $sortType = 'desc';
        }

        switch ($sortBy) {
            case 'name':
                $query->whereHas('translations', function($query) use ($sortType) {
                    $query
                    ->where('lang_key', 'name')
                    ->where('lang_id', 2)
                    ->orderBy('lang_value', 'desc');
                });
                break;
            // default:
            //     $query->orderBy('created_at', $sortType);
        }

        return $query;
    }
}
