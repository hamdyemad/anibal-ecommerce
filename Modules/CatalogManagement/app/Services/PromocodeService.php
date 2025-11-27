<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Interfaces\PromocodeRepositoryInterface;

class PromocodeService
{
    public function __construct(
        protected PromocodeRepositoryInterface $promocodeRepository
    ) {}

    public function getAllPromocodes(array $filters = [], int $perPage = 15)
    {
        return $this->promocodeRepository->getAllPromocodes($filters, $perPage);
    }

    public function getPromocodeById(int $id)
    {
        return $this->promocodeRepository->getPromocodeById($id);
    }

    public function createPromocode(array $data)
    {
        return $this->promocodeRepository->createPromocode($data);
    }

    public function updatePromocode(int $id, array $data)
    {
        return $this->promocodeRepository->updatePromocode($id, $data);
    }

    public function deletePromocode(int $id)
    {
        return $this->promocodeRepository->deletePromocode($id);
    }
}
