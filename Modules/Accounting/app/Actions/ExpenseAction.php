<?php

namespace Modules\Accounting\app\Actions;

use Modules\Accounting\app\Services\ExpenseService;
use App\Traits\Res;
use Illuminate\Support\Str;

class ExpenseAction
{
    use Res;

    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    public function getDataTable($data)
    {
        try {
            $perPage = isset($data['length']) && $data['length'] > 0 ? (int)$data['length'] : 10;
            $start = isset($data['start']) && $data['start'] >= 0 ? (int)$data['start'] : 0;
            $page = $perPage > 0 ? floor($start / $perPage) + 1 : 1;

            $orderColumnIndex = $data['orderColumnIndex'] ?? 0;
            $orderDirection = $data['orderDirection'] ?? 'desc';

            $filters = [
                'search' => $data['search'],
                'expense_item_id' => $data['expense_item_id'],
                'date_from' => $data['date_from'],
                'date_to' => $data['date_to'],
            ];

            $totalRecords = $this->expenseService->getExpensesQuery([])->count();
            $filteredRecords = $this->expenseService->getExpensesQuery($filters)->count();

            $dataPaginated = $this->expenseService->getAllExpenses($filters, $perPage, $page, $orderDirection);

            $formattedData = [];
            foreach ($dataPaginated as $item) {
                $receiptButton = '';
                if ($item->receipt) {
                    $receiptButton = '<a href="'.asset('storage/'.$item->receipt).'" target="_blank" class="btn btn-info btn-sm" title="View Receipt">
                        <i class="uil uil-file-alt"></i> View Receipt
                    </a>';
                } else {
                    $receiptButton = '<span class="text-muted">No Receipt</span>';
                }

                $actions = '
                    <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                        <button type="button" class="edit btn btn-warning table_action_father" title="Edit"
                                data-bs-toggle="modal" data-bs-target="#editExpenseModal"
                                data-id="'.$item->id.'" data-expense-item-id="'.$item->expense_item_id.'"
                                data-amount="'.$item->amount.'" data-description="'.$item->description.'"
                                data-expense-date="'.$item->expense_date->format('Y-m-d').'">
                            <i class="uil uil-edit table_action_icon"></i>
                        </button>
                        <button type="button" class="btn btn-danger table_action_father delete-expense" title="Delete" 
                                data-bs-toggle="modal" data-bs-target="#modal-delete-expense"
                                data-id="'.$item->id.'" data-name="Expense #'.$item->id.'"
                                data-url="'.route('admin.accounting.expenses.destroy', $item->id).'">
                            <i class="uil uil-trash-alt table_action_icon"></i>
                        </button>
                    </div>';

                $formattedData[] = [
                    'category' => '<div class="d-flex"><div class="userDatatable-inline-title"><h6 class="text-dark fw-500">'.($item->expenseItem ? $item->expenseItem->name : 'N/A').'</h6></div></div>',
                    'amount' => '<span class="fw-bold text-success">'.currency().' '.number_format($item->amount, 2).'</span>',
                    'description' => '<p class="mb-0">'.Str::limit($item->description, 50).'</p>',
                    'expense_date' => $item->expense_date->format('Y-m-d'),
                    'receipt' => $receiptButton,
                    'created_at' => $item->created_at,
                    'actions' => $actions
                ];
            }

            return [
                'data' => $formattedData,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $dataPaginated
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}


