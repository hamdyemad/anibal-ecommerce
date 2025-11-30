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
                $query->join('translations', function($join) {
                    $join->on('brands.id', '=', 'translations.translatable_id')
                         ->where('translations.translatable_type', 'Modules\\CatalogManagement\\app\\Models\\Brand')
                         ->where('translations.lang_key', 'name');
                })
                ->orderBy('translations.lang_value', $sortType)
                ->select('brands.*')->distinct('brands.id');
                break;
            default:
                $query->orderBy('created_at', $sortType);
        }

        return $query;
    }   
}
