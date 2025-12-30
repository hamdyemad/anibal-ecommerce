<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\Interfaces\Api\ProductApiRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\CatalogManagement\app\DTOs\BrandFilterDTO;
use Modules\CatalogManagement\app\DTOs\ProductFilterDTO;
use Modules\CategoryManagment\app\Services\Api\CategoryApiService;

class ProductApiService
{
    public function __construct(
        protected ProductApiRepositoryInterface $repository,
        protected CategoryApiService $CategoryService,
        protected BrandApiService $BrandService
    ) {}

    /**
     * Get all products with filtering and pagination
     */
    public function getAllProducts(ProductFilterDTO $dto)
    {
        return $this->repository->getAllProducts($dto);
    }

    /**
     * Get specific product by ID or slug
     */
    public function getProductByIdOrSlug(string $identifier, string $vendorId)
    {
        $product = $this->repository->getProductByIdOrSlug($identifier, $vendorId);

        if ($product) {
            // Increment views
            $this->repository->incrementProductViews($product->id);
        }

        return $product;
    }

    public function incrementProductSales($productId, $quantity = 1)
    {
        return $this->repository->incrementProductSales($productId, $quantity);
    }


    public function findProduct(string $productId)
    {
        return $this->repository->findProduct($productId);
    }

    /**
     * Find vendor product with all relationships for order creation pipeline
     */
    public function findProductForOrder(string $productId)
    {
        return $this->repository->findProductForOrder($productId);
    }

    /**
     * Get filters (categories, brands, variants, etc.)
     */
    public function getFilters(array $filters)
    {
        // Check if filtering by occasion or bundle
        $hasOccasionFilter = !empty($filters['occasion_id']);
        $hasBundleFilter = !empty($filters['bundle_category_id']);
        
        // If filtering by occasion, get filters from occasion products
        if ($hasOccasionFilter) {
            $occasionFilters = $this->repository->getFiltersByOccasion($filters);
            return [
                'category_info' => [],
                'categories' => $this->getCategoriesFromOccasion($filters),
                'brands' => $occasionFilters['brands'] ?? [],
                'price' => $this->getPriceFromOccasion($filters),
                'tags' => [],
                'trees' => $occasionFilters['trees'] ?? [],
            ];
        }
        
        // If filtering by bundle category, get filters from bundle products
        if ($hasBundleFilter) {
            $bundleFilters = $this->repository->getFiltersByBundle($filters);
            return [
                'category_info' => [],
                'categories' => $this->getCategoriesFromBundle($filters),
                'brands' => $bundleFilters['brands'] ?? [],
                'price' => $this->getPriceFromBundle($filters),
                'tags' => [],
                'trees' => $bundleFilters['trees'] ?? [],
            ];
        }
        
        // Standard filtering - get brands filtered by products
        $filteredBrands = $this->repository->getBrandsByProductFilters($filters);
        
        return [
            'category_info' => $this->CategoryService->getCategoriesByIds($filters),
            'categories' => $this->CategoryService->getCategoriesByFilters($filters),
            'brands' => $filteredBrands,
            'price' => $this->repository->getPriceByFilters($filters),
            'tags' => $this->repository->getTagsByFilters($filters),
            'trees' => $this->repository->getTreesByFilters($filters),
        ];
    }
    
    /**
     * Get categories (departments) from occasion products
     */
    private function getCategoriesFromOccasion(array $filters)
    {
        $query = \Modules\CatalogManagement\app\Models\OccasionProduct::query()
            ->whereHas('occasion', function ($q) use ($filters) {
                $q->where('is_active', true)
                  ->where('end_date', '>=', now()->toDateString());
                if (!empty($filters['occasion_id'])) {
                    $q->where('id', $filters['occasion_id']);
                }
            });
        
        $variantIds = $query->pluck('vendor_product_variant_id')->unique()->toArray();
        
        $vendorProductIds = \Modules\CatalogManagement\app\Models\VendorProductVariant::whereIn('id', $variantIds)
            ->pluck('vendor_product_id')->unique()->toArray();
        
        $productIds = \Modules\CatalogManagement\app\Models\VendorProduct::whereIn('id', $vendorProductIds)
            ->pluck('product_id')->unique()->toArray();
        
        $departmentIds = \Modules\CatalogManagement\app\Models\Product::whereIn('id', $productIds)
            ->whereNotNull('department_id')
            ->pluck('department_id')->unique()->toArray();
        
        return \Modules\CategoryManagment\app\Models\Department::whereIn('id', $departmentIds)
            ->where('active', true)
            ->get()
            ->map(function ($dept) {
                return [
                    'id' => $dept->id,
                    'title' => $dept->name,
                    'slug' => $dept->slug,
                    'image' => '',
                    'icon' => '',
                    'type' => 'department',
                ];
            })->toArray();
    }
    
    /**
     * Get categories (departments) from bundle products
     */
    private function getCategoriesFromBundle(array $filters)
    {
        $query = \Modules\CatalogManagement\app\Models\BundleProduct::query()
            ->whereHas('bundle', function ($q) use ($filters) {
                $q->where('is_active', true)->where('admin_approval', 1);
                if (!empty($filters['bundle_category_id'])) {
                    $q->where('bundle_category_id', $filters['bundle_category_id']);
                }
            });
        
        $variantIds = $query->pluck('vendor_product_variant_id')->unique()->toArray();
        
        $vendorProductIds = \Modules\CatalogManagement\app\Models\VendorProductVariant::whereIn('id', $variantIds)
            ->pluck('vendor_product_id')->unique()->toArray();
        
        $productIds = \Modules\CatalogManagement\app\Models\VendorProduct::whereIn('id', $vendorProductIds)
            ->pluck('product_id')->unique()->toArray();
        
        $departmentIds = \Modules\CatalogManagement\app\Models\Product::whereIn('id', $productIds)
            ->whereNotNull('department_id')
            ->pluck('department_id')->unique()->toArray();
        
        return \Modules\CategoryManagment\app\Models\Department::whereIn('id', $departmentIds)
            ->where('active', true)
            ->get()
            ->map(function ($dept) {
                return [
                    'id' => $dept->id,
                    'title' => $dept->name,
                    'slug' => $dept->slug,
                    'image' => '',
                    'icon' => '',
                    'type' => 'department',
                ];
            })->toArray();
    }
    
    /**
     * Get max price from occasion products
     */
    private function getPriceFromOccasion(array $filters)
    {
        $query = \Modules\CatalogManagement\app\Models\OccasionProduct::query()
            ->whereHas('occasion', function ($q) use ($filters) {
                $q->where('is_active', true)
                  ->where('end_date', '>=', now()->toDateString());
                if (!empty($filters['occasion_id'])) {
                    $q->where('id', $filters['occasion_id']);
                }
            });
        
        return $query->max('special_price') ?? 0;
    }
    
    /**
     * Get max price from bundle products
     */
    private function getPriceFromBundle(array $filters)
    {
        $query = \Modules\CatalogManagement\app\Models\BundleProduct::query()
            ->whereHas('bundle', function ($q) use ($filters) {
                $q->where('is_active', true)->where('admin_approval', 1);
                if (!empty($filters['bundle_category_id'])) {
                    $q->where('bundle_category_id', $filters['bundle_category_id']);
                }
            });
        
        return $query->max('price') ?? 0;
    }

    /**
     * Get filters by occasion
     * TODO: Uncomment when Occasion model is created
     */
    // public function getFiltersByOccasion(array $filters)
    // {
    //     return $this->repository->getFiltersByOccasion($filters);
    // }

    /**
     * Get filters by bundle category
     * TODO: Uncomment when BundleCategory model is created
     */
    // public function getFiltersByBundleCategory(array $filters)
    // {
    //     return $this->repository->getFiltersByBundleCategory($filters);
    // }

    /**
     * Get filters (trees and brands) by occasion products
     */
    public function getFiltersByOccasion(array $filters)
    {
        return $this->repository->getFiltersByOccasion($filters);
    }

    /**
     * Get filters (trees and brands) by bundle products
     */
    public function getFiltersByBundle(array $filters)
    {
        return $this->repository->getFiltersByBundle($filters);
    }

    /**
     * Get variant trees from filtered products
     */
    public function getTreesByFilters(array $filters)
    {
        return $this->repository->getTreesByFilters($filters);
    }

    /**
     * Get all vendor product variants with their product details
     * Used for order creation
     */
    public function getVariantsWithProduct(array $filters)
    {
        return $this->repository->getVariantsWithProduct($filters);
    }

    /**
     * Get product by slug with all vendors, prices, and stock
     */
    public function getProductBySlug(string $slug)
    {
        return $this->repository->getProductBySlug($slug);
    }
}
