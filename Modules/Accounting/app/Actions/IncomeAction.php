<?php

namespace Modules\Accounting\Actions;

use Modules\Accounting\Services\IncomeService;

class IncomeAction
{
    public function __construct(
        protected IncomeService $incomeService
    ) {}

    public function getDataTable(array $data)
    {
        $filters = [
            'search' => $data['search'] ?? null,
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
        ];

        $perPage = $data['per_page'] ?? 10;
        $page = $data['page'] ?? 1;
        $orderDirection = $data['orderDirection'] ?? 'desc';

        $dataPaginated = $this->incomeService->getAllIncomeEntries($filters, $perPage, $page, $orderDirection);

        $formattedData = $dataPaginated->map(function ($entry) {
            return [
                'id' => $entry->id,
                'order_number' => $entry->order->order_number ?? 'N/A',
                'vendor_name' => $entry->vendor->name ?? 'N/A',
                'amount' => number_format($entry->amount, 2) . ' ' . currency(),
                'commission_amount' => number_format($entry->commission_amount ?? 0, 2) . ' ' . currency(),
                'vendor_amount' => number_format($entry->vendor_amount ?? 0, 2) . ' ' . currency(),
                'description' => $entry->description ?? '',
                'created_at' => $entry->created_at,
            ];
        });

        return [
            'data' => $formattedData,
            'totalRecords' => $dataPaginated->total(),
            'filteredRecords' => $dataPaginated->total(),
            'dataPaginated' => $dataPaginated
        ];
    }
}
