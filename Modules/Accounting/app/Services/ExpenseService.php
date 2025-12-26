<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Contracts\ExpenseRepositoryInterface;

class ExpenseService
{
    public function __construct(
        protected ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function getAllExpenses(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc')
    {
        return $this->expenseRepository->getAllExpenses($filters, $perPage, $page, $orderDirection);
    }

    public function getExpensesQuery(array $filters = [])
    {
        return $this->expenseRepository->getExpensesQuery($filters);
    }

    public function create(array $data)
    {
        $expense = $this->expenseRepository->create($data);

        // Handle receipt file upload if present
        if (isset($data['receipt_file'])) {
            $this->handleReceiptUpload($expense, $data['receipt_file']);
        }

        return $expense;
    }

    public function update(string $id, array $data)
    {
        $expense = $this->expenseRepository->update($id, $data);

        // Handle receipt file upload if present
        if (isset($data['receipt_file'])) {
            $this->handleReceiptUpload($expense, $data['receipt_file']);
        }

        return $expense;
    }

    public function delete(string $id)
    {
        return $this->expenseRepository->delete($id);
    }

    private function handleReceiptUpload($expense, $file)
    {
        if ($file) {
            $expense->attachments()->where('type', 'receipt')->delete();
            $path = $file->store('expenses/receipts', 'public');
            $expense->attachments()->create([
                'path' => $path,
                'type' => 'receipt',
            ]);
        }
    }
}
