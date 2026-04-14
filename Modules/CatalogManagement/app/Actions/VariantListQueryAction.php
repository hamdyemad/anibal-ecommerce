<?php

namespace Modules\CatalogManagement\app\Actions;

use Modules\CatalogManagement\app\Models\VendorProductVariant;

class VariantListQueryAction
{
    /**
     * Query for variant listing
     * Returns variants with their vendor product and product info
     */
    public function handle(array $filters = [])
    {
        $query = VendorProductVariant::query()
            ->whereHas('vendorProduct', function($q) {
                $q->where('is_active', true)
                  ->where('status', 'approved');
            })
            ->where('price', '>', 0)
            ->with([
                'vendorProduct' => function($q) {
                    $q->with([
                        'product.translations',
                        'product.mainImage',
                        'product.brand.translations',
                        'product.department.translations',
                        'product.category.translations',
                        'product.subCategory.translations',
                        'vendor.translations',
                        'taxes.translations',
                    ]);
                },
                'variantConfiguration.translations',
                'variantConfiguration.key.translations',
                'variantLink', // Load the variant link to build tree
            ]);

        // Apply filters from vendor product
        if (!empty($filters)) {
            // Search filter - handle separately for better performance
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($searchQuery) use ($search) {
                    // Search in variant SKU
                    $searchQuery->where('vendor_product_variants.sku', 'like', "%{$search}%")
                        // Search in vendor product SKU
                        ->orWhereHas('vendorProduct', function($vpQ) use ($search) {
                            $vpQ->where('vendor_products.sku', 'like', "%{$search}%");
                        })
                        // Search in product name
                        ->orWhereHas('vendorProduct.product.translations', function($transQ) use ($search) {
                            $transQ->where('lang_value', 'like', "%{$search}%");
                        });
                });
            }
            
            $query->whereHas('vendorProduct', function($q) use ($filters) {
                // Department filter
                if (!empty($filters['department_id'])) {
                    $q->whereHas('product', function($subQ) use ($filters) {
                        $subQ->whereHas('department', function($deptQ) use ($filters) {
                            $deptQ->where('id', $filters['department_id'])
                                  ->orWhere('slug', $filters['department_id']);
                        });
                    });
                }

                // Category filter
                if (!empty($filters['main_category_id']) || !empty($filters['category_id'])) {
                    $categoryId = $filters['main_category_id'] ?? $filters['category_id'];
                    $q->whereHas('product', function($subQ) use ($categoryId) {
                        $subQ->whereHas('category', function($catQ) use ($categoryId) {
                            $catQ->where('id', $categoryId)
                                 ->orWhere('slug', $categoryId);
                        });
                    });
                }

                // SubCategory filter
                if (!empty($filters['sub_category_id'])) {
                    $q->whereHas('product', function($subQ) use ($filters) {
                        $subQ->whereHas('subCategory', function($subCatQ) use ($filters) {
                            $subCatQ->where('id', $filters['sub_category_id'])
                                    ->orWhere('slug', $filters['sub_category_id']);
                        });
                    });
                }

                // Brand filter
                if (!empty($filters['brand_id'])) {
                    $q->whereHas('product', function($subQ) use ($filters) {
                        $subQ->whereHas('brand', function($brandQ) use ($filters) {
                            $brandQ->where('id', $filters['brand_id'])
                                   ->orWhere('slug', $filters['brand_id']);
                        });
                    });
                }

                // Vendor filter
                if (!empty($filters['vendor_id'])) {
                    $q->whereHas('vendor', function($vendorQ) use ($filters) {
                        $vendorQ->where('id', $filters['vendor_id'])
                                ->orWhere('slug', $filters['vendor_id']);
                    });
                }
            });
        }

        // Price filters
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        $query = $this->applySorting($query, $filters);

        return $query;
    }

    protected function applySorting($query, array $filters)
    {
        $sortBy = $filters['sort_by'] ?? 'sort_number';
        $sortType = $filters['sort_type'] ?? 'asc';

        if (!in_array($sortType, ['asc', 'desc'])) {
            $sortType = 'asc';
        }

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortType);
                break;
                
            case 'created_at':
                $query->orderBy('created_at', $sortType);
                break;
                
            case 'sort_number':
            default:
                // Sort by vendor product sort_number
                $query->join('vendor_products', 'vendor_product_variants.vendor_product_id', '=', 'vendor_products.id')
                      ->select('vendor_product_variants.*')
                      ->orderBy('vendor_products.sort_number', $sortType)
                      ->orderBy('vendor_product_variants.id', 'asc'); // Secondary sort by variant id
        }

        return $query;
    }
}
