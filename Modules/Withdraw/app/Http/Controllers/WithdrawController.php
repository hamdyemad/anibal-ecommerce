<?php

namespace Modules\Withdraw\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Withdraw\app\Services\WithdrawService;

class WithdrawController extends Controller
{
    public function __construct(
        protected WithdrawService $withdrawService,
    )
    {}

    public function sendMoney()
    {
        $response = $this->withdrawService->sendMoney();
        return "here" ;
        return view('withdraw::index');
    }
}
