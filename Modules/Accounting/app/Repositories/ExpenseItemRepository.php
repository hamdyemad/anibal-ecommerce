<?php

namespace Modules\Accounting\Repositories;

use Modules\Accounting\app\Models\ExpenseItem;
use Modules\Accounting\Contracts\ExpenseItemRepositoryInterface;

class ExpenseItemRepository implements ExpenseItemRepositoryInterface
{
    public function getAllExpenseItems(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc')
    {
        $query = $this->getExpenseItemsQuery($filters);

        if ($orderDirection === 'desc') {
            $query->latest();
        } else {
            $query->oldest();
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getExpenseItemsQuery(array $filters = [])
    {
        $query = ExpenseItem::filter($filters);

        return $query;
    }

    public function create(array $data)
    {
        return ExpenseItem::create($data);
    }

    public function update(string $id, array $data)
    {
        $expenseItem = ExpenseItem::findOrFail($id);
        $expenseItem->update($data);
        return $expenseItem;
    }

    public function delete(string $id)
    {
        $expenseItem = ExpenseItem::findOrFail($id);
        return $expenseItem->delete();
    }
}
