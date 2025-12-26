<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounting\Http\Requests\BalancesRequest;
use Modules\Accounting\Services\BalanceService;
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
