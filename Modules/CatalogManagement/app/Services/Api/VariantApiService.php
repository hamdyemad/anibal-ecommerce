<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\DTOs\ProductFilterDTO;
use Modules\CatalogManagement\app\Repositories\Api\VariantApiRepository;

class VariantApiService
{
    public function __construct(
        protected VariantApiRepository $variantRepository
    ) {}

    /**
     * Get all variants with filters and pagination
     */
    public function getAllVariants(ProductFilterDTO $dto)
    {
        return $this->variantRepository->getAllVariants($dto);
    }
}
