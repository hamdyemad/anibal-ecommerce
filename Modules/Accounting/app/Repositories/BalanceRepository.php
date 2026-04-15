<?php

namespace Modules\Accounting\app\Repositories;

use Modules\Accounting\app\Contracts\BalanceRepositoryInterface;
use Modules\Accounting\app\Models\VendorBalance;

class BalanceRepository implements BalanceRepositoryInterface
{
    public function getVendorBalances(array $filters = [])
    {
        $query = VendorBalance::with('vendor');
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vendor', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if (!empty($filters['min_balance'])) {
            $query->where('available_balance', '>=', $filters['min_balance']);
        }
        
        return $query->latest()->paginate(20);
    }
}
