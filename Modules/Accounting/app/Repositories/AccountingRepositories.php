<?php

namespace Modules\Accounting\Repositories;

use Modules\Accounting\Contracts\SummaryRepositoryInterface;
use Modules\Accounting\Contracts\IncomeRepositoryInterface;
use Modules\Accounting\Contracts\BalanceRepositoryInterface;
use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Accounting\app\Models\VendorBalance;
use Modules\Accounting\app\Models\Expense;

class SummaryRepository implements SummaryRepositoryInterface
{
    public function getSummaryData(array $filters = []): array
    {
        $query = AccountingEntry::query();
        $expenseQuery = Expense::query();
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
            $expenseQuery->whereDate('expense_date', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
            $expenseQuery->whereDate('expense_date', '<=', $filters['date_to']);
        }
        
        return [
            'total_income' => (clone $query)->income()->sum('amount'),
            'total_expenses' => $expenseQuery->sum('amount'),
            'total_commissions' => (clone $query)->income()->sum('commission_amount'),
            'total_refunds' => abs((clone $query)->refund()->sum('amount')),
            'net_profit' => (clone $query)->income()->sum('amount') - $expenseQuery->sum('amount')
        ];
    }
}

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
