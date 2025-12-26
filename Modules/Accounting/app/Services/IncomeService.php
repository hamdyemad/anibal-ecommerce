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
}
