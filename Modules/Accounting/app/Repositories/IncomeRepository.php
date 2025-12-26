<?php

namespace Modules\Accounting\Repositories;

use Modules\Accounting\Contracts\IncomeRepositoryInterface;
use Modules\Accounting\app\Models\AccountingEntry;

class IncomeRepository implements IncomeRepositoryInterface
{
    public function getIncomeEntries(array $filters = [])
    {
        return AccountingEntry::income()
            ->with(['order', 'vendor'])
            ->filter($filters)
            ->latest()
            ->paginate(
                perPage: $filters['per_page'] ?? 20,
                page: $filters['page'] ?? 1,
                columns: ['*']
            );
    }
}
