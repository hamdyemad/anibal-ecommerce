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
            // Get order products for this vendor (only from delivered orders)
            $deliverStageIds = \Modules\Order\app\Models\OrderStage::withoutGlobalScopes()
                ->where('type', 'deliver')
                ->pluck('id')
                ->toArray();
            
            $orderProducts = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $balance->vendor_id)
                ->whereIn('stage_id', $deliverStageIds)
                ->with(['vendorProduct.product.department'])
                ->get();
            
            // Get orders for shipping calculation
            $orderIds = $orderProducts->pluck('order_id')->unique();
            $vendorOrders = \Modules\Order\app\Models\Order::whereIn('id', $orderIds)->get();

            // price already includes (unit_price * quantity), so just sum it
            $totalEarnings = $orderProducts->sum('price');
            
            // Add shipping cost from order products
            $totalShipping = $orderProducts->sum('shipping_cost');
            $totalEarningsWithShipping = $totalEarnings + $totalShipping;
            
            // Commission calculation - use commission field from order_products
            $totalCommission = $orderProducts->sum(function($product) {
                $productTotal = $product->price + ($product->shipping_cost ?? 0);
                $commissionPercent = $product->commission > 0 
                    ? $product->commission 
                    : ($product->vendorProduct?->product?->department?->commission ?? 0);
                return $productTotal * ($commissionPercent / 100);
            });
            
            $availableBalance = $totalEarningsWithShipping - $totalCommission;

            $totalWithdrawn = $balance->withdraws()->where('status', 'accepted')->sum('sent_amount');
            $actualAvailableBalance = $availableBalance - $totalWithdrawn;

            return [
                'id' => $balance->id,
                'vendor_name' => $balance->vendor->user->name ?? $balance->vendor->name ?? 'N/A',
                'vendor_email' => $balance->vendor->user->email ?? $balance->vendor->email ?? 'N/A',
                'total_earnings' => number_format($totalEarningsWithShipping, 2) . ' ' . currency(),
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


