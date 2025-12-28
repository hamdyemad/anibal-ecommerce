<?php

namespace Modules\Accounting\app\Services;

use Modules\Accounting\app\Contracts\SummaryRepositoryInterface;
use Modules\Accounting\app\Contracts\IncomeRepositoryInterface;
use Modules\Accounting\app\Contracts\BalanceRepositoryInterface;

class SummaryService
{
    public function __construct(private SummaryRepositoryInterface $repository) {}

    public function getSummary(array $filters = []): array
    {
        return $this->repository->getSummaryData($filters);
    }
}

class IncomeService
{
    public function __construct(private IncomeRepositoryInterface $repository) {}

    public function getIncomeEntries(array $filters = [])
    {
        return $this->repository->getIncomeEntries($filters);
    }
}

class BalanceService
{
    public function __construct(private BalanceRepositoryInterface $repository) {}

    public function getVendorBalances(array $filters = [])
    {
        return $this->repository->getVendorBalances($filters);
    }
}


