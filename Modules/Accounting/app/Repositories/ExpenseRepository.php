<?php

namespace Modules\Accounting\Repositories;

use Modules\Accounting\app\Models\Expense;
use Modules\Accounting\Contracts\ExpenseRepositoryInterface;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function getAllExpenses(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc')
    {
        return Expense::with(['expenseItem'])
            ->filter($filters)
            ->orderBy('expense_date', $orderDirection)
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getExpensesQuery(array $filters = [])
    {
        return Expense::with(['expenseItem'])->filter($filters);
    }

    public function create(array $data)
    {
        return Expense::create($data);
    }

    public function update(string $id, array $data)
    {
        $expense = Expense::findOrFail($id);
        $expense->update($data);
        return $expense;
    }

    public function delete(string $id)
    {
        $expense = Expense::findOrFail($id);
        return $expense->delete();
    }
}
