<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Contracts\BalanceRepositoryInterface;

class BalanceService
{
    public function __construct(private BalanceRepositoryInterface $repository) {}

    public function getVendorBalances(array $filters = [])
    {
        return $this->repository->getVendorBalances($filters);
    }
}
