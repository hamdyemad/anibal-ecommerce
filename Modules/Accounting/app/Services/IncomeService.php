<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Contracts\IncomeRepositoryInterface;

class IncomeService
{
    public function __construct(private IncomeRepositoryInterface $repository) {}

    public function getIncomeEntries(array $filters = [])
    {
        return $this->repository->getIncomeEntries($filters);
    }

    public function getAllIncomeEntries(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc')
    {
        return $this->repository->getAllIncomeEntries($filters, $perPage, $page, $orderDirection);
    }

    public function getIncomeEntriesQuery(array $filters = [])
    {
        return $this->repository->getIncomeEntriesQuery($filters);
    }
}
