<?php

namespace Modules\Accounting\Contracts;

interface IncomeRepositoryInterface
{
    public function getIncomeEntries(array $filters = []);
}
