<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Contracts\SummaryRepositoryInterface;
use Modules\Accounting\Contracts\IncomeRepositoryInterface;
use Modules\Accounting\Contracts\BalanceRepositoryInterface;

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
