<?php

namespace Modules\Accounting\app\Contracts;

interface SummaryRepositoryInterface
{
    public function getSummaryData(array $filters = []): array;
}
