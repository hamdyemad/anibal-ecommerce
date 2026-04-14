<?php

namespace Modules\CatalogManagement\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\CatalogManagement\app\Actions\VariantListQueryAction;
use Modules\CatalogManagement\app\DTOs\ProductFilterDTO;

class VariantApiRepository
{
    public function __construct(
        protected VariantListQueryAction $query,
        protected IsPaginatedAction $paginated
    ) {}

    /**
     * Get all variants with filters and pagination
     */
    public function getAllVariants(ProductFilterDTO $dto)
    {
        $filters = $dto->toArray();
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $dto->per_page, $dto->paginated);
        return $result;
    }
}
