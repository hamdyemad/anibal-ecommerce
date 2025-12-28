<?php

namespace Modules\Accounting\app\Contracts;

interface VendorBalanceRepositoryInterface
{
    public function getAllVendorBalances(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc');
    public function getVendorBalancesQuery(array $filters = []);
}

