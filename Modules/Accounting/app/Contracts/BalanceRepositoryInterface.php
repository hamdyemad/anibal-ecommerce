<?php

namespace Modules\Accounting\app\Contracts;

interface BalanceRepositoryInterface
{
    public function getVendorBalances(array $filters = []);
}

