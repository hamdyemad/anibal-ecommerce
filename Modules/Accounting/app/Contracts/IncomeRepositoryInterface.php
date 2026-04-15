<?php

namespace Modules\Accounting\app\Contracts;

interface IncomeRepositoryInterface
{
    public function getIncomeEntries(array $filters = []);
}
