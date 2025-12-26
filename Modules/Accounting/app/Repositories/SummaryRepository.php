<?php

namespace Modules\Accounting\Repositories;

use Modules\Accounting\Contracts\SummaryRepositoryInterface;
use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Accounting\app\Models\Expense;

class SummaryRepository implements SummaryRepositoryInterface
{
    public function getSummaryData(array $filters = []): array
    {
        $query = AccountingEntry::query();
        $expenseQuery = Expense::query();
        
        // Use scope filters for AccountingEntry
        $query->filter($filters);
        
        // Use scope filters for Expense with date mapping
        $expenseFilters = $filters;
        if (!empty($filters['date_from'])) {
            $expenseFilters['created_date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $expenseFilters['created_date_to'] = $filters['date_to'];
        }
        $expenseQuery->filter($expenseFilters);
        
        return [
            'total_income' => (clone $query)->income()->sum('amount'),
            'total_expenses' => $expenseQuery->sum('amount'),
            'total_commissions' => (clone $query)->income()->sum('commission_amount'),
            'total_refunds' => abs((clone $query)->refund()->sum('amount')),
            'net_profit' => (clone $query)->income()->sum('amount') - $expenseQuery->sum('amount')
        ];
    }
}
