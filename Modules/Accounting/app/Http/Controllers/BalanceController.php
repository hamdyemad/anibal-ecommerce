<?php

namespace Modules\Accounting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounting\app\Http\Requests\BalancesRequest;
use Modules\Accounting\app\Services\BalanceService;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function __construct(private BalanceService $balanceService) {}

    public function index(BalancesRequest $request)
    {
        $balances = $this->balanceService->getVendorBalances($request->validated());
        return view('accounting::balances', compact('balances'));
    }
}


