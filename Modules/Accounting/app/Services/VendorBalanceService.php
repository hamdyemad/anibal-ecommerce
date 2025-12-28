<?php

namespace Modules\Accounting\app\Services;

use Modules\Accounting\app\Contracts\VendorBalanceRepositoryInterface;

class VendorBalanceService
{
    public function __construct(
        protected VendorBalanceRepositoryInterface $vendorBalanceRepository
    ) {}

    public function getAllVendorBalances(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc')
    {
        return $this->vendorBalanceRepository->getAllVendorBalances($filters, $perPage, $page, $orderDirection);
    }

    public function getVendorBalancesQuery(array $filters = [])
    {
        return $this->vendorBalanceRepository->getVendorBalancesQuery($filters);
    }
}


