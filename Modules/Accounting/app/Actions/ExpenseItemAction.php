<?php

namespace Modules\Accounting\app\Actions;

use Modules\Accounting\app\Services\ExpenseItemService;
use App\Services\LanguageService;
use App\Traits\Res;
use Illuminate\Support\Str;

class ExpenseItemAction
{
    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected ExpenseItemService $expenseItemService
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
                'active' => $data['active'],
            ];

            $languages = $this->languageService->getAll();

            $totalRecords = $this->expenseItemService->getExpenseItemsQuery([])->count();
            $filteredRecords = $this->expenseItemService->getExpenseItemsQuery($filters)->count();

            $dataPaginated = $this->expenseItemService->getAllExpenseItems($filters, $perPage, $page, $orderDirection);

            $formattedData = [];
            foreach ($dataPaginated as $item) {
                $statusBadge = $item->active;

                // Build translations object for JavaScript
                $translationsData = [];
                foreach ($languages as $language) {
                    $translationsData[$language->id] = [
                        'name' => $item->getTranslation('name', $language->code) ?? ''
                    ];
                }
                $translationsJson = htmlspecialchars(json_encode($translationsData), ENT_QUOTES, 'UTF-8');

                $actions = '
                    <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                        <button type="button" class="edit btn btn-warning table_action_father" title="Edit"
                                data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                data-id="'.$item->id.'" data-translations=\''.$translationsJson.'\'
                                data-active="'.$item->active.'">
                            <i class="uil uil-edit table_action_icon"></i>
                        </button>
                        <button type="button" class="btn btn-danger table_action_father delete-expense-item" title="Delete"
                                data-bs-toggle="modal" data-bs-target="#modal-delete-expense-item"
                                data-id="'.$item->id.'" data-name="'.$item->name.'"
                                data-url="'.route('admin.accounting.expense-items.destroy', $item->id).'">
                            <i class="uil uil-trash-alt table_action_icon"></i>
                        </button>
                    </div>';

                $formattedData[] = [
                    'name' => '<div class="d-flex"><div class="userDatatable-inline-title"><h6 class="text-dark fw-500">'.$item->name.'</h6></div></div>',
                    'active' => $statusBadge,
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


