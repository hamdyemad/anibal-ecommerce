<?php

namespace Modules\Accounting\app\Repositories;

use Modules\Accounting\app\Contracts\IncomeRepositoryInterface;
use Modules\Accounting\app\Models\AccountingEntry;

class IncomeRepository implements IncomeRepositoryInterface
{
    public function getIncomeEntries(array $filters = [])
    {
        return AccountingEntry::income()
            ->with(['order', 'vendor'])
            ->filter($filters)
            ->latest()
            ->paginate(
                perPage: $filters['per_page'] ?? 20,
                page: $filters['page'] ?? 1,
                columns: ['*']
            );
    }

    public function getAllIncomeEntries(array $filters = [], int $perPage = 15, int $page = 1, string $orderDirection = 'desc')
    {
        $query = $this->getIncomeEntriesQuery($filters);
        return $query->orderBy('created_at', $orderDirection)->paginate($perPage, ['*'], 'page', $page);
    }

    public function getIncomeEntriesQuery(array $filters = [])
    {
        // Include both income and refund entries
        $query = AccountingEntry::whereIn('type', ['income', 'refund'])->with(['order', 'vendor.user']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($subQ) use ($search) {
                    $subQ->where('order_number', 'like', "%{$search}%");
                })
                ->orWhereHas('vendor', function ($q) use ($search) {
                    $q->whereHas('translations', function($subQ) use ($search) {
                        $subQ->where('lang_value', 'like', "%{$search}%");
                    });
                })
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }
}


