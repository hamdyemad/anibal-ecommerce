<?php

namespace Modules\Accounting\Actions;

use Modules\Accounting\Services\VendorBalanceService;

class VendorBalanceAction
{
    public function __construct(
        protected VendorBalanceService $vendorBalanceService
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

        $dataPaginated = $this->vendorBalanceService->getAllVendorBalances($filters, $perPage, $page, $orderDirection);

        $formattedData = $dataPaginated->map(function ($balance) {
            return [
                'id' => $balance->id,
                'vendor_name' => $balance->vendor->name ?? 'N/A',
                'vendor_email' => $balance->vendor->email ?? 'N/A',
                'total_earnings' => number_format($balance->total_earnings, 2) . ' ' . currency(),
                'commission_deducted' => number_format($balance->commission_deducted, 2) . ' ' . currency(),
                'available_balance' => number_format($balance->available_balance, 2) . ' ' . currency(),
                'withdrawn_amount' => number_format($balance->withdrawn_amount, 2) . ' ' . currency(),
                'updated_at' => $balance->updated_at,
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
