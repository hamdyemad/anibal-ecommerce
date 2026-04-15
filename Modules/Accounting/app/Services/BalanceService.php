<?php

namespace Modules\Accounting\app\Services;

use Modules\Accounting\app\Contracts\BalanceRepositoryInterface;

class BalanceService
{
    public function __construct(private BalanceRepositoryInterface $repository) {}

    public function getVendorBalances(array $filters = [])
    {
        return $this->repository->getVendorBalances($filters);
    }
}
