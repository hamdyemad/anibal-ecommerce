<?php

namespace Modules\Withdraw\app\Repositories;

use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;
use Modules\Vendor\app\Models\Vendor;
use Modules\Withdraw\app\Interfaces\WithdrawRepositoryInterface;
use Modules\Withdraw\app\Models\Withdraw;

class WithdrawRepository implements WithdrawRepositoryInterface
{
    /**
     * Get all departments with filters and pagination
     */
    public function getVendor()
    {
        return Vendor::latest()
            ->with(['translations' => function ($query) {
                $query->where('lang_key', 'name');
            }])
            ->get()
            ->map(function ($vendor) {
                $vendor->translation_name = $vendor->translations->first();
                return $vendor;
            });
    }

    public function getVendorBalance($vendor_id)
    {
        // Get the vendor to access the user_id
        $vendor = Vendor::find($vendor_id);

        if (!$vendor || !$vendor->user_id) {
            return [
                "orders_price" => "0.000",
                "vendor_commission" => 0,
                "total_vendor_balance" => "0.000",
                "total_sent_money" => "0.000",
                "remaining" => "0.000",
                "bnaia_balance" => "0.000",
                "waiting_approve_requests" => "0.000"
            ];
        }

        $orders = OrderProduct::where("vendor_id", $vendor_id)->get();

        $total_vendor_balance = $orders->sum("price") - ($orders->sum("price") * ($orders->first()->commission / 100));

        $total_sent_money = Withdraw::where(function ($q) use ($vendor) {
            $q->where('reciever_id', $vendor->id);
        })
            ->where('status', 'accepted')
            ->sum('sent_amount');

        $waiting_approve_requests = Withdraw::where(function ($q) use ($vendor) {
            $q->where('reciever_id', $vendor->id);
        })
            ->where('status', 'new')
            ->sum('sent_amount');

        return [
            "orders_price" => number_format($orders->sum("price"), 3),
            "vendor_commission" => $orders->first()->commission,
            "total_vendor_balance" =>  number_format($total_vendor_balance, 3),


            "total_sent_money" => number_format($total_sent_money, 3),
            "remaining" => number_format($total_vendor_balance - $total_sent_money, 3),
            "bnaia_balance" => number_format($orders->sum("price") * ($orders->first()->commission / 100), 3),

            "waiting_approve_requests" => number_format($waiting_approve_requests, 3)
        ];
    }
}
