<?php

namespace Modules\Accounting\app\Contracts;

interface ExpenseRepositoryInterface
{
    public function getAllExpenses(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc');
    public function getExpensesQuery(array $filters = []);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}

