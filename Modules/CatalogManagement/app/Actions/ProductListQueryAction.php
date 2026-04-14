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
                'product.department.translations',
                'product.category.translations',
                'product.subCategory.translations',
                'vendor.translations',
                'taxes.translations',
            ])
            // Add minimum price as a subquery to avoid N+1
            ->addSelect([
                'min_variant_price' => \Modules\CatalogManagement\app\Models\VendorProductVariant::selectRaw('MIN(price)')
                    ->whereColumn('vendor_product_id', 'vendor_products.id')
                    ->where('price', '>', 0)
                    ->limit(1)
            ])
            // Add minimum variant has_discount flag
            ->addSelect([
                'min_variant_has_discount' => \Modules\CatalogManagement\app\Models\VendorProductVariant::select('has_discount')
                    ->whereColumn('vendor_product_id', 'vendor_products.id')
                    ->where('price', '>', 0)
                    ->orderBy('price', 'asc')
                    ->limit(1)
            ])
            // Add minimum variant price_before_discount
            ->addSelect([
                'min_variant_price_before_discount' => \Modules\CatalogManagement\app\Models\VendorProductVariant::select('price_before_discount')
                    ->whereColumn('vendor_product_id', 'vendor_products.id')
                    ->where('price', '>', 0)
                    ->orderBy('price', 'asc')
                    ->limit(1)
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

        // Price filters - filter by minimum variant price INCLUDING TAXES
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            // Get tax rate for the current country
            $taxRate = 0;
            $countryId = $filters['country_id'] ?? null;
            
            // If no country_id in filters, try to get from session or header
            if (!$countryId) {
                $countryCode = session('country_code') ?? request()->header('Country-Code');
                if ($countryCode) {
                    $country = \Modules\AreaSettings\app\Models\Country::where('code', strtoupper($countryCode))->first();
                    $countryId = $country?->id;
                }
            }
            
            if ($countryId) {
                $taxRate = \Modules\CatalogManagement\app\Models\Tax::where('country_id', $countryId)
                    ->sum('percentage');
            }
            
            // Convert user's price (with taxes) to price before taxes for filtering
            $minPriceBeforeTax = null;
            $maxPriceBeforeTax = null;
            
            if (!empty($filters['min_price'])) {
                // min_price_with_tax = min_price_before_tax * (1 + tax_rate/100)
                // min_price_before_tax = min_price_with_tax / (1 + tax_rate/100)
                $minPriceBeforeTax = $taxRate > 0 
                    ? $filters['min_price'] / (1 + ($taxRate / 100))
                    : $filters['min_price'];
            }
            
            if (!empty($filters['max_price'])) {
                $maxPriceBeforeTax = $taxRate > 0
                    ? $filters['max_price'] / (1 + ($taxRate / 100))
                    : $filters['max_price'];
            }
            
            // Use whereRaw with subquery to filter by minimum price
            if ($minPriceBeforeTax) {
                $query->whereRaw('(
                    SELECT MIN(price) 
                    FROM vendor_product_variants 
                    WHERE vendor_product_variants.vendor_product_id = vendor_products.id 
                    AND vendor_product_variants.price > 0
                    AND vendor_product_variants.deleted_at IS NULL
                ) >= ?', [$minPriceBeforeTax]);
            }
            
            if ($maxPriceBeforeTax) {
                $query->whereRaw('(
                    SELECT MIN(price) 
                    FROM vendor_product_variants 
                    WHERE vendor_product_variants.vendor_product_id = vendor_products.id 
                    AND vendor_product_variants.price > 0
                    AND vendor_product_variants.deleted_at IS NULL
                ) <= ?', [$maxPriceBeforeTax]);
            }
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
                // Use the min_variant_price subquery that's already added in the main query
                $query->orderBy('min_variant_price', $sortType);
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
