<?php

namespace Modules\Accounting\Contracts;

interface IncomeRepositoryInterface
{
    public function getIncomeEntries(array $filters = []);
    public function getAllIncomeEntries(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc');
    public function getIncomeEntriesQuery(array $filters = []);
}
