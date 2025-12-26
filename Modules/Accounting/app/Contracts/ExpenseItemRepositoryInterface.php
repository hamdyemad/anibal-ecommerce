<?php

namespace Modules\Accounting\Contracts;

interface ExpenseItemRepositoryInterface
{
    public function getAllExpenseItems(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc');
    public function getExpenseItemsQuery(array $filters = []);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}
