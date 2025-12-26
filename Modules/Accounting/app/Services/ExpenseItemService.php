<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Contracts\ExpenseItemRepositoryInterface;

class ExpenseItemService
{
    public function __construct(
        protected ExpenseItemRepositoryInterface $expenseItemRepository
    ) {}

    public function getAllExpenseItems(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc')
    {
        return $this->expenseItemRepository->getAllExpenseItems($filters, $perPage, $page, $orderDirection);
    }

    public function getExpenseItemsQuery(array $filters = [])
    {
        return $this->expenseItemRepository->getExpenseItemsQuery($filters);
    }

    public function create(array $data)
    {
        $data['active'] = ($data['active'] ?? null) == '1';
        
        $expenseItem = $this->expenseItemRepository->create($data);
        
        // Handle translations
        if (isset($data['name_en'])) {
            $expenseItem->setTranslation('name', 'en', $data['name_en']);
        }
        if (isset($data['name_ar'])) {
            $expenseItem->setTranslation('name', 'ar', $data['name_ar']);
        }
        
        return $expenseItem;
    }

    public function update(string $id, array $data)
    {
        $data['active'] = ($data['active'] ?? null) == '1';
        
        $expenseItem = $this->expenseItemRepository->update($id, $data);
        
        // Handle translations
        if (isset($data['name_en'])) {
            $expenseItem->setTranslation('name', 'en', $data['name_en']);
        }
        if (isset($data['name_ar'])) {
            $expenseItem->setTranslation('name', 'ar', $data['name_ar']);
        }
        
        return $expenseItem;
    }

    public function delete(string $id)
    {
        return $this->expenseItemRepository->delete($id);
    }
}
