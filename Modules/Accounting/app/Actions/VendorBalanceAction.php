<?php

namespace Modules\Accounting\app\Actions;

use Modules\Accounting\app\Services\VendorBalanceService;

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

        // Add vendor filter if user is vendor
        if (isVendor()) {
            $filters['vendor_id'] = auth()->user()->vendor->id ?? null;
        }

        $perPage = $data['per_page'] ?? 10;
        $page = $data['page'] ?? 1;
        $orderDirection = $data['orderDirection'] ?? 'desc';

        $dataPaginated = $this->vendorBalanceService->getAllVendorBalances($filters, $perPage, $page, $orderDirection);

        $formattedData = $dataPaginated->map(function ($balance) {
            // Get order products for this vendor
            $orderProducts = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $balance->vendor_id)
                ->with(['vendorProduct.product.department'])
                ->get();
            
            // Get orders for shipping calculation
            $orderIds = $orderProducts->pluck('order_id')->unique();
            $vendorOrders = \Modules\Order\app\Models\Order::whereIn('id', $orderIds)->get();

            $totalEarnings = $orderProducts->sum(function($p) {
                return $p->price * $p->quantity;
            });
            
            // Commission calculation - group by order to add shipping only once per order
            $totalCommission = 0;
            $productsByOrder = $orderProducts->groupBy('order_id');
            foreach ($productsByOrder as $orderId => $orderProductsGroup) {
                $order = $vendorOrders->firstWhere('id', $orderId);
                $shipping = $order ? $order->shipping : 0;
                $orderProductsTotal = $orderProductsGroup->sum('price');
                
                // Get commission percentage from first product's department
                $commissionPercent = $orderProductsGroup->first()->vendorProduct?->product?->department?->commission ?? 0;
                
                // Commission = (products total + shipping) × percentage / 100
                $totalCommission += (($orderProductsTotal + $shipping) * $commissionPercent) / 100;
            }
            
            $availableBalance = $totalEarnings - $totalCommission;

            $totalWithdrawn = $balance->withdraws()->where('status', 'accepted')->sum('sent_amount');
            $actualAvailableBalance = $availableBalance - $totalWithdrawn;

            return [
                'id' => $balance->id,
                'vendor_name' => $balance->vendor->user->name ?? $balance->vendor->name ?? 'N/A',
                'vendor_email' => $balance->vendor->user->email ?? $balance->vendor->email ?? 'N/A',
                'total_earnings' => number_format($totalEarnings, 2) . ' ' . currency(),
                'commission_deducted' => number_format($totalCommission, 2) . ' ' . currency(),
                'available_balance' => number_format($availableBalance, 2) . ' ' . currency(),
                'total_withdrawn' => number_format($totalWithdrawn, 2) . ' ' . currency(),
                'actual_available_balance' => number_format($actualAvailableBalance, 2) . ' ' . currency(),
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


