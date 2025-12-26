<?php

namespace Modules\Accounting\Contracts;

interface SummaryRepositoryInterface
{
    public function getSummaryData(array $filters = []): array;
}
