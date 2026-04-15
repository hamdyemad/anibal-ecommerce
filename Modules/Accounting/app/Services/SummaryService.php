<?php

namespace Modules\Accounting\app\Services;

use Modules\Accounting\app\Contracts\SummaryRepositoryInterface;

class SummaryService
{
    public function __construct(private SummaryRepositoryInterface $repository) {}

    public function getSummary(array $filters = []): array
    {
        return $this->repository->getSummaryData($filters);
    }
}
