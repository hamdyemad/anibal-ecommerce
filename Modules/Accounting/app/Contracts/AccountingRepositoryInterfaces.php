<?php

namespace Modules\Accounting\Contracts;

interface SummaryRepositoryInterface
{
    public function getSummaryData(array $filters = []): array;
}

interface IncomeRepositoryInterface
{
    public function getIncomeEntries(array $filters = []);
}

interface BalanceRepositoryInterface
{
    public function getVendorBalances(array $filters = []);
}
