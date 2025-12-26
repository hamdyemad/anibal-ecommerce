<?php

namespace Modules\Accounting\Contracts;

interface BalanceRepositoryInterface
{
    public function getVendorBalances(array $filters = []);
}
