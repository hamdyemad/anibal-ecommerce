<?php

namespace App\Http\Controllers;

use Modules\Withdraw\app\Models\Withdraw;

class DashboardController extends Controller
{
    public function index()
    {
        $vendor = auth()->user()->vendor;

        $all_transactions = [];

        if ($vendor) {
            $all_transactions = Withdraw::with([ "vendor" => function($vendor){
                $vendor->with("translations")->first();
            }])->where('reciever_id', $vendor->id)
                ->latest()
                ->limit(10)
                ->get();
        } else {
            $all_transactions = Withdraw::with([ "vendor" => function($vendor){
                $vendor->with("translations")->first();
            }])->latest()->limit(10)->get();
        }

        return $all_transactions;
        $data = [
            'title' => __('menu.dashboard.title'),
        ];
        return view('pages.dashboard.dashboard', $data);
    }
}
