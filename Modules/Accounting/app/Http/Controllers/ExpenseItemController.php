<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounting\Http\Requests\StoreExpenseItemRequest;
use Modules\Accounting\Http\Requests\UpdateExpenseItemRequest;
use Modules\Accounting\Services\ExpenseItemService;
use App\Services\LanguageService;
use Modules\Accounting\Actions\ExpenseItemAction;
use Illuminate\Http\Request;

class ExpenseItemController extends Controller
{
    public function __construct(
        protected ExpenseItemService $expenseItemService,
        protected LanguageService $languageService,
        protected ExpenseItemAction $expenseItemAction
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
            'active' => $request->get('active'),
        ];

        try {
            $response = $this->expenseItemAction->getDataTable($data);

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
        $languages = $this->languageService->getAll();
        return view('accounting::expense-items', compact('languages'));
    }

    public function store($lang, $countryCode, StoreExpenseItemRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->expenseItemService->create($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Expense category created successfully.',
                    'redirect' => route('admin.accounting.expense-items')
                ]);
            }

            return redirect()->route('admin.accounting.expense-items')
                ->with('success', 'Expense category created successfully.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating expense category'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error creating expense category')
                ->withInput();
        }
    }

    public function update(UpdateExpenseItemRequest $request, $lang, $countryCode, string $id)
    {
        $validated = $request->validated();

        try {
            $this->expenseItemService->update($id, $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Expense category updated successfully.',
                    'redirect' => route('admin.accounting.expense-items')
                ]);
            }

            return redirect()->route('admin.accounting.expense-items')
                ->with('success', 'Expense category updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating expense category'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error updating expense category')
                ->withInput();
        }
    }

    public function destroy($lang, $countryCode, string $id)
    {
        try {
            $this->expenseItemService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Expense category deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting expense category'
            ], 500);
        }
    }
}
