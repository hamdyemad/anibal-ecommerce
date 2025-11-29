<?php

namespace Modules\CatalogManagement\app\Interfaces\Api;

use Modules\CatalogManagement\app\DTOs\BrandFilterDTO;

interface BrandApiRepositoryInterface
{
    /**
     * Get all brands with filters and pagination
     */
    public function getAllBrands(BrandFilterDTO $filters);

    /**
     * Get brand by ID
     */
    public function find(BrandFilterDTO $filters, $id);
}
