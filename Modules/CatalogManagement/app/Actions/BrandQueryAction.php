<?php

namespace Modules\CatalogManagement\app\Actions;

use App\Models\Language;
use App\Models\Translation;
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

        $langId = Language::where('code', app()->getLocale())->value('id');
        switch ($sortBy) {
            case 'name':
                $query->whereHas('translations', function ($q) use ($langId) {
                    $q->where('translatable_type', Brand::class)
                    ->where('lang_key', 'name')
                    ->where('lang_id', $langId);
                })
                ->orderBy(
                    Translation::select('lang_value')
                        ->whereColumn('translatable_id', 'brands.id')
                        ->where('translatable_type', Brand::class)
                        ->where('lang_key', 'name')
                        ->where('lang_id', $langId)
                        ->limit(1),
                    $sortType
                )
                ;
            default:
                $query->orderBy('created_at', $sortType);
        }

        return $query;
    }
}
