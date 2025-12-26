<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounting\Http\Requests\AccountingSummaryRequest;
use Modules\Accounting\Services\SummaryService;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function __construct(private SummaryService $summaryService) {}

    public function index(AccountingSummaryRequest $request)
    {
        $summary = $this->summaryService->getSummary($request->validated());
        return view('accounting::summary', compact('summary'));
    }
}
