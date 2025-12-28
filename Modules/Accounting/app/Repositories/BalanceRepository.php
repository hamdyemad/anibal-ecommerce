<?php

namespace Modules\Accounting\app\Repositories;

use Modules\Accounting\app\Contracts\BalanceRepositoryInterface;
use Modules\Accounting\app\Models\VendorBalance;

class BalanceRepository implements BalanceRepositoryInterface
{
    public function getVendorBalances(array $filters = [])
    {
        return VendorBalance::with('vendor')
            ->filter($filters)
            ->latest()
            ->paginate(
                perPage: $filters['per_page'] ?? 20,
                page: $filters['page'] ?? 1,
                columns: ['*']
            );
    }
}


