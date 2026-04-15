<?php

namespace Modules\Accounting\app\Services;

use Modules\Accounting\app\Contracts\IncomeRepositoryInterface;

class IncomeService
{
    public function __construct(private IncomeRepositoryInterface $repository) {}

    public function getIncomeEntries(array $filters = [])
    {
        return $this->repository->getIncomeEntries($filters);
    }
}
