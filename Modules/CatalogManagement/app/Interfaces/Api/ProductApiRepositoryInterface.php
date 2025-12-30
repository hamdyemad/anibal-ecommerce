<?php

namespace Modules\CatalogManagement\app\Interfaces\Api;

use Modules\CatalogManagement\app\DTOs\ProductFilterDTO;

interface ProductApiRepositoryInterface
{
    public function getAllProducts(ProductFilterDTO $filters);
    public function getProductByIdOrSlug(string $identifier, string $vendorId);
    public function findProduct(string $id);
    public function getPriceByFilters(array $filters);
    public function getTagsByFilters(array $filters);
    public function getTreesByFilters(array $filters);
    public function getBrandsByProductFilters(array $filters);
    public function incrementProductViews(string $productId);
    public function incrementProductSales(string $productId, $quantity);
    public function getVariantsWithProduct(array $filters);
    public function findProductForOrder(string $id);
    public function getProductBySlug(string $slug);
    public function getFiltersByOccasion(array $filters);
    public function getFiltersByBundle(array $filters);
}
