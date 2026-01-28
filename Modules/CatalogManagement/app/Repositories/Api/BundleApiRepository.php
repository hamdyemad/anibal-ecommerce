<?php

namespace Modules\CatalogManagement\app\Repositories\Api;

use Modules\CatalogManagement\app\Models\Bundle;
use App\Models\Language;
use App\Models\Attachment;
use App\Services\CacheService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\CatalogManagement\app\Interfaces\Api\BundleRepositoryApiInterface;

class BundleApiRepository implements BundleRepositoryApiInterface
{
    protected $bundle;
    protected CacheService $cache;

    public function __construct(Bundle $bundle, CacheService $cache)
    {
        $this->bundle = $bundle;
        $this->cache = $cache;
    }

    /**
     * Get all bundles with filters
     */
    public function getAllBundles($filters = [], $perPage = 15)
    {
        $cacheKey = $this->cache->key('BundleApi', 'all', array_merge($filters, ['per_page' => $perPage]));
        
        return $this->cache->remember($cacheKey, function() use ($filters, $perPage) {
            $query = Bundle::with([
                'vendor.translations',
                'bundleCategory.translations',
                'main_image',
                'translations',
                'bundleProducts' => function ($q) {
                    $q->with([
                        'vendorProductVariant.vendorProduct' => function ($vpQuery) {
                            $vpQuery->with([
                                'product.mainImage',
                                'product.brand.translations',
                                'product.department.translations',
                                'product.category.translations',
                                'product.subCategory.translations',
                                'vendor.translations',
                                'taxes',
                                'variants.variantConfiguration.parent_data.key',
                                'variants.variantConfiguration.key'
                            ])
                            ->withCount('reviews')
                            ->withAvg('reviews', 'star');
                        },
                        'vendorProductVariant.variantConfiguration.parent_data.key',
                        'vendorProductVariant.variantConfiguration.key'
                    ]);
                }
            ])
            ->withCount('bundleProducts')
            ->withSum('bundleProducts as total_price_sum', 'price')
            ->filter($filters)
            ->active()
            ->latest();
            
            return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
        }, 300); // 5 minutes cache
    }

    /**
     * Get bundle by ID
     */
    public function getBundleById($id, array $filters = [])
    {
        $cacheKey = $this->cache->key('BundleApi', 'find', array_merge(['id' => $id], $filters));
        
        return $this->cache->remember($cacheKey, function() use ($id, $filters) {
            return Bundle::with([
                'attachments',
                'country',
                'vendor',
                'bundleCategory',
                'main_image',
                'bundleProducts' => function ($q) use ($filters) {
                    // Only include products where vendor product is active and product is approved
                    $q->whereHas('vendorProductVariant', function ($vpvQuery) {
                        $vpvQuery->whereHas('vendorProduct', function ($vpQuery) {
                            $vpQuery
                            ->where('vendor_products.status', 'approved')
                            ->where('is_active', '1');
                        });
                    });
                    
                    // Apply search filter on bundle products via VendorProduct filter scope
                    if (!empty($filters['search'])) {
                        $q->whereHas('vendorProductVariant.vendorProduct', function ($vpQuery) use ($filters) {
                            $vpQuery->filter($filters);
                        });
                    }
                    
                    // Eager load relationships with optimized loading
                    $q->with([
                        'vendorProductVariant.vendorProduct' => function ($vpQuery) {
                            $vpQuery->with([
                                'product.mainImage',
                                'product.brand.translations',
                                'product.department.translations',
                                'product.category.translations',
                                'product.subCategory.translations',
                                'vendor.translations',
                                'taxes',
                                'variants.variantConfiguration.parent_data.key',
                                'variants.variantConfiguration.key'
                            ])
                            ->withCount('reviews')
                            ->withAvg('reviews', 'star');
                        },
                        'vendorProductVariant.variantConfiguration.parent_data.key',
                        'vendorProductVariant.variantConfiguration.key'
                    ]);
                },
            ])
            ->withCount(['bundleProducts' => function ($q) {
                // Only count products where vendor product is active and product is approved
                $q->whereHas('vendorProductVariant', function ($vpvQuery) {
                    $vpvQuery->whereHas('vendorProduct', function ($vpQuery) {
                        $vpQuery
                            ->where('vendor_products.status', 'approved')
                            ->where('is_active', '1');
                    });
                });
            }])
            ->where(function($q) use ($id) {
                $q->where('id', $id)->orWhere('slug', $id);
            })
            ->active()
            ->firstOrFail();
        }, 600); // 10 minutes cache
    }

    /**
     * Clear bundle API cache
     */
    public function clearCache(): void
    {
        $this->cache->forgetByPattern('bundleapi:*');
    }

}
