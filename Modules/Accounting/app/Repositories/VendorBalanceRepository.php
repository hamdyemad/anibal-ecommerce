<?php

namespace Modules\Accounting\Repositories;

use Modules\Accounting\app\Models\VendorBalance;
use Modules\Accounting\Contracts\VendorBalanceRepositoryInterface;

class VendorBalanceRepository implements VendorBalanceRepositoryInterface
{
    public function getAllVendorBalances(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc')
    {
        $query = $this->getVendorBalancesQuery($filters);
        return $query->orderBy('updated_at', $orderDirection)->paginate($perPage, ['*'], 'page', $page);
    }

    public function getVendorBalancesQuery(array $filters = [])
    {
        $query = VendorBalance::with(['vendor']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vendor', function ($q) use ($search) {
                $q->whereHas('translations', function($subQ) use ($search) {
                    $subQ->where('lang_value', 'like', "%{$search}%");
                });
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('updated_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('updated_at', '<=', $filters['date_to']);
        }

        return $query;
    }
}
