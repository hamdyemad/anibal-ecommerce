<?php

namespace Modules\Accounting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounting\app\Services\IncomeService;
use Modules\Accounting\app\Actions\IncomeAction;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function __construct(
        protected IncomeService $incomeService,
        protected IncomeAction $incomeAction
    ) {}

    public function datatable(Request $request)
    {
        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'per_page' => $request->get('per_page', $request->get('length', 10)),
            'orderColumnIndex' => $request->get('orderColumnIndex', 0),
            'orderDirection' => $request->get('orderDirection', 'desc'),
            'search' => $request->get('search'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        try {
            $response = $this->incomeAction->getDataTable($data);

            return response()->json([
                'draw' => $data['draw'],
                'data' => $response['data'],
                'recordsTotal' => $response['totalRecords'],
                'recordsFiltered' => $response['filteredRecords'],
                'current_page' => $response['dataPaginated']->currentPage(),
                'last_page' => $response['dataPaginated']->lastPage(),
                'per_page' => $response['dataPaginated']->perPage(),
                'total' => $response['dataPaginated']->total(),
                'from' => $response['dataPaginated']->firstItem(),
                'to' => $response['dataPaginated']->lastItem()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $data['draw'],
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        return view('accounting::income');
    }
}


