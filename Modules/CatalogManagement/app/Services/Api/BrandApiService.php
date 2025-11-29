<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\DTOs\BrandFilterDTO;
use Modules\CatalogManagement\app\Interfaces\Api\BrandApiRepositoryInterface;

class BrandApiService
{
    protected $BrandRepository;

    public function __construct(
        BrandApiRepositoryInterface $BrandRepository
    ) {
        $this->BrandRepository = $BrandRepository;
    }

    /**
     * Get all brands with filters and pagination
     */
    public function getAllBrands(BrandFilterDTO $dto)
    {
        return $this->BrandRepository->getAllBrands($dto);
    }

    /**
     * Get Brand by ID
     */
    public function find(BrandFilterDTO $dto, $id)
    {
        return $this->BrandRepository->find($dto, $id);
    }
}
