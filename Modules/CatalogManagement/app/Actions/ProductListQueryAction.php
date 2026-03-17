<?php

namespace Modules\CatalogManagement\app\Actions;

use App\Models\Language;
use Modules\CatalogManagement\app\Models\VendorProduct;

class ProductListQueryAction
{
    /**
     * Lightweight query for product listing pages
     * Only loads essential relations needed for product cards
     */
    public function handle(array $filters = [])
    {
        $query = VendorProduct::query()
            ->active()
            ->status(VendorProduct::STATUS_APPROVED)
            ->with([
                'product.translations',
                'product.mainImage',
                'product.brand.translations',
                'variants',
                'vendor.translations',
                'taxes.translations',
            ])
            ->withCount(['reviews' => function($q) {
                $q->withoutGlobalScope('country_filter');
            }])
            ->withAvg(['reviews' => function($q) {
                $q->withoutGlobalScope('country_filter');
            }], 'star')
            ->withExists(['wishlist as is_fav' => function($q) {
                $user = auth()->user() ?? auth()->guard('sanctum')->user();
                if (!$user) {
                    $q->whereRaw('1=0');
                } else {
                    $q->where('customer_id', $user->id);
                }
            }]);

        // Price filters
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $query->whereHas('variants', function ($q) use ($filters) {
                if (!empty($filters['min_price'])) {
                    $q->where('price', '>=', $filters['min_price']);
                }
                if (!empty($filters['max_price'])) {
                    $q->where('price', '<=', $filters['max_price']);
                }
                $q->where('price', '>', 0);
            });
        }

        if (!empty($filters)) {
            $query->filter($filters);
        }

        $query = $this->applySorting($query, $filters);

        return $query;
    }

    protected function applySorting($query, array $filters)
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortType = $filters['sort_type'] ?? 'asc';

        if (!in_array($sortType, ['asc', 'desc'])) {
            $sortType = 'desc';
        }

        switch ($sortBy) {
            case 'name':
                $currentLocale = app()->getLocale();
                $langId = Language::where('code', $currentLocale)->value('id');
                
                if (!$langId) {
                    $langId = $currentLocale === 'ar' ? 1 : 2;
                }
                
                $sortDirection = strtolower($sortType) === 'asc' ? 'ASC' : 'DESC';
                
                $query->leftJoin('products', 'vendor_products.product_id', '=', 'products.id')
                    ->leftJoin('translations', function($join) use ($langId) {
                        $join->on('products.id', '=', 'translations.translatable_id')
                             ->where('translations.translatable_type', '=', 'Modules\\CatalogManagement\\app\\Models\\Product')
                             ->where('translations.lang_key', '=', 'name')
                             ->where('translations.lang_id', '=', $langId);
                    })
                    ->select('vendor_products.*')
                    ->groupBy('vendor_products.id')
                    ->orderByRaw("COALESCE(MAX(translations.lang_value), vendor_products.id) COLLATE utf8mb4_unicode_ci {$sortDirection}");
                break;
                
            case 'price':
                $query->leftJoin('vendor_product_variants', 'vendor_products.id', '=', 'vendor_product_variants.vendor_product_id')
                    ->select('vendor_products.*')
                    ->selectRaw('MIN(vendor_product_variants.price) as min_price')
                    ->groupBy('vendor_products.id')
                    ->orderBy('min_price', $sortType);
                break;
                
            case 'rating':
                $query->orderBy('reviews_avg_star', $sortType);
                break;
                
            case 'views':
                $query->orderBy('vendor_products.views', $sortType);
                break;
                
            case 'sales':
                $query->orderBy('vendor_products.sales', $sortType);
                break;
                
            default:
                $query->orderBy('vendor_products.sort_number', $sortType);
        }

        return $query;
    }
}
