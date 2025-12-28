<?php

namespace Modules\Accounting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounting\app\Http\Requests\StoreExpenseRequest;
use Modules\Accounting\app\Http\Requests\UpdateExpenseRequest;
use Modules\Accounting\app\Services\ExpenseService;
use Modules\Accounting\app\Services\ExpenseItemService;
use Modules\Accounting\app\Actions\ExpenseAction;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService,
        protected ExpenseItemService $expenseItemService,
        protected ExpenseAction $expenseAction
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
            'expense_item_id' => $request->get('expense_item_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        try {
            $response = $this->expenseAction->getDataTable($data);

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
        $expenseItems = $this->expenseItemService->getAllExpenseItems(['active' => 1], 1000);
        return view('accounting::expenses', compact('expenseItems'));
    }

    public function store($lang, $countryCode, StoreExpenseRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->expenseService->create($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('accounting.record_created'),
                    'redirect' => route('admin.accounting.expenses')
                ]);
            }

            return redirect()->route('admin.accounting.expenses')
                ->with('success', __('accounting.record_created'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('accounting.error_creating')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('accounting.error_creating'))
                ->withInput();
        }
    }

    public function update(UpdateExpenseRequest $request, $lang, $countryCode, string $id)
    {
        $validated = $request->validated();

        try {
            $this->expenseService->update($id, $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('accounting.record_updated'),
                    'redirect' => route('admin.accounting.expenses')
                ]);
            }

            return redirect()->route('admin.accounting.expenses')
                ->with('success', __('accounting.record_updated'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('accounting.error_updating')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('accounting.error_updating'))
                ->withInput();
        }
    }

    public function destroy($lang, $countryCode, string $id)
    {
        try {
            $this->expenseService->delete($id);

            return response()->json([
                'success' => true,
                'message' => __('accounting.expense_deleted_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('accounting.error_deleting_expense')
            ], 500);
        }
    }
}


