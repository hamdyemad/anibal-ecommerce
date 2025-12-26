<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounting\Http\Requests\IncomeRequest;
use Modules\Accounting\Services\IncomeService;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function __construct(private IncomeService $incomeService) {}

    public function index(IncomeRequest $request)
    {
        $entries = $this->incomeService->getIncomeEntries($request->validated());
        return view('accounting::income', compact('entries'));
    }
}
