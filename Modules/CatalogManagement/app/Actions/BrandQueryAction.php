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

        // Apply products count with combined filters (optimized)
        $query->withCount(['products as products_count' => function ($q) use ($filters) {
            // Only count active and approved vendor products
            $q->whereHas('vendorProducts', function ($vq) use ($filters) {
                $vq->where('is_active', 1)->where('status', 'approved');
                
                // Vendor filter for product count
                if (isset($filters['vendor_id'])) {
                    $vendorIdentifier = $filters['vendor_id'];
                    // Try to find vendor by slug or ID (cached)
                    $vendorId = \Cache::remember('vendor_id_' . $vendorIdentifier, 3600, function() use ($vendorIdentifier) {
                        return \Modules\Vendor\app\Models\Vendor::where('slug', $vendorIdentifier)
                            ->orWhere('id', $vendorIdentifier)
                            ->value('id');
                    });
                    
                    if ($vendorId) {
                        $vq->where('vendor_id', $vendorId);
                    }
                }
            });

            // Department filter for product count
            if (isset($filters['department_id'])) {
                $deptId = $filters['department_id'];
                $q->where('department_id', $deptId);
            }
        }]);

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
                $query->orderBy('sort_number', 'asc');
        }

        return $query;
    }
}
