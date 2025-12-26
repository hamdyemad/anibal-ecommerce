<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Contracts\SummaryRepositoryInterface;

class SummaryService
{
    public function __construct(private SummaryRepositoryInterface $repository) {}

    public function getSummary(array $filters = []): array
    {
        return $this->repository->getSummaryData($filters);
    }
}
