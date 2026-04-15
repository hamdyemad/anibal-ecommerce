<?php

namespace Modules\Accounting\app\Repositories;

use Modules\Accounting\app\Contracts\IncomeRepositoryInterface;
use Modules\Accounting\app\Models\AccountingEntry;

class IncomeRepository implements IncomeRepositoryInterface
{
    public function getIncomeEntries(array $filters = [])
    {
        $query = AccountingEntry::income()->with(['order', 'vendor']);
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('order', function($subQ) use ($search) {
                    $subQ->where('order_number', 'like', "%{$search}%");
                })->orWhereHas('vendor', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        return $query->latest()->paginate(20);
    }
}
