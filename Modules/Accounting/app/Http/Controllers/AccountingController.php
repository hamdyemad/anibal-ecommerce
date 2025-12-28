<?php

namespace Modules\Accounting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounting\app\Services\AccountingService;
use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Accounting\app\Models\VendorBalance;
use Modules\Accounting\app\Models\ExpenseItem;
use Modules\Accounting\app\Models\Expense;
use Modules\Accounting\app\Http\Requests\StoreExpenseItemRequest;
use Modules\Accounting\app\Http\Requests\UpdateExpenseItemRequest;
use Modules\Accounting\app\Http\Requests\StoreExpenseRequest;
use Modules\Accounting\app\Http\Requests\UpdateExpenseRequest;
use Modules\Accounting\app\Http\Requests\AccountingSummaryRequest;
use Modules\Accounting\app\Http\Requests\IncomeRequest;
use Modules\Accounting\app\Http\Requests\BalancesRequest;
use Modules\Accounting\app\Services\ExpenseItemService;
use Modules\Accounting\app\Services\ExpenseService;
use Modules\Accounting\app\Services\SummaryService;
use Modules\Accounting\app\Services\IncomeService;
use Modules\Accounting\app\Services\BalanceService;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    protected $accountingService;

    public function __construct(
        AccountingService $accountingService,
        private ExpenseItemService $expenseItemService,
        private ExpenseService $expenseService,
        private SummaryService $summaryService,
        private IncomeService $incomeService,
        private BalanceService $balanceService
    ) {
        $this->accountingService = $accountingService;
    }

    public function summary(AccountingSummaryRequest $request)
    {
        $summary = $this->summaryService->getSummary($request->validated());
        return view('accounting::summary', compact('summary'));
    }

    public function income(Request $request)
    {
        $query = AccountingEntry::income()->with(['order', 'vendor']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('order', function($subQ) use ($search) {
                    $subQ->where('order_number', 'like', "%{$search}%");
                })->orWhereHas('vendor', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $entries = $query->latest()->paginate(20);
        return view('accounting::income', compact('entries'));
    }

    public function expenseItems(Request $request)
    {
        $filters = $request->only(['search', 'status', 'per_page', 'page']);
        
        $items = ExpenseItem::query()
            ->filter($filters)
            ->latest()
            ->paginate(
                perPage: $request->per_page ?? 20,
                page: $request->page ?? 1,
                columns: ['*']
            );
            
        return view('accounting::expense-items', compact('items'));
    }

    public function expenses(Request $request)
    {
        $filters = $request->only(['search', 'date_from', 'date_to', 'per_page', 'page']);
        
        $expenses = Expense::with('expenseItem')
            ->filter($filters)
            ->latest()
            ->paginate(
                perPage: $request->per_page ?? 20,
                page: $request->page ?? 1,
                columns: ['*']
            );
            
        $expenseItems = ExpenseItem::where('active', true)->orderBy('name')->get();
        
        return view('accounting::expenses', compact('expenses', 'expenseItems'));
    }

    public function balances(Request $request)
    {
        $query = VendorBalance::with('vendor');
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('vendor', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('min_balance')) {
            $query->where('available_balance', '>=', $request->min_balance);
        }
        
        $balances = $query->latest()->paginate(20);
        return view('accounting::balances', compact('balances'));
    }

    // Expense Item CRUD methods
    public function storeExpenseItem(StoreExpenseItemRequest $request)
    {
        $this->expenseItemService->create($request->validated());
        return redirect()->route('admin.accounting.expense-items')
            ->with('success', 'Expense category created successfully.');
    }

    public function updateExpenseItem(UpdateExpenseItemRequest $request, $expense_item)
    {
        $expenseItem = ExpenseItem::findOrFail($expense_item);
        $this->expenseItemService->update($expenseItem, $request->validated());
        return redirect()->route('admin.accounting.expense-items')
            ->with('success', 'Expense category updated successfully.');
    }

    public function destroyExpenseItem($expense_item)
    {
        $expenseItem = ExpenseItem::findOrFail($expense_item);
        $this->expenseItemService->delete($expenseItem);
        return redirect()->route('admin.accounting.expense-items')
            ->with('success', 'Expense category deleted successfully.');
    }

    // Expense CRUD methods
    public function storeExpense(StoreExpenseRequest $request)
    {
        $this->expenseService->create($request->validated());
        return redirect()->route('admin.accounting.expenses')
            ->with('success', 'Expense created successfully.');
    }

    public function updateExpense(UpdateExpenseRequest $request, Expense $expense)
    {
        $this->expenseService->update($expense, $request->validated());
        return redirect()->route('admin.accounting.expenses')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroyExpense(Expense $expense)
    {
        $this->expenseService->delete($expense);
        return redirect()->route('admin.accounting.expenses')
            ->with('success', 'Expense deleted successfully.');
    }
}


