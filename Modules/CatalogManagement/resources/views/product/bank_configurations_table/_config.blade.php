{{-- Bank Products DataTable Configuration --}}
@php
    // Page title
    $pageTitle = trans('catalogmanagement::product.bank_products_management');

    // Breadcrumb items
    $breadcrumbItems = [
        [
            'title' => trans('dashboard.title'),
            'url' => route('admin.dashboard'),
            'icon' => 'uil uil-estate',
        ],
        ['title' => $pageTitle],
    ];

    // Table headers
    $tableHeaders = [
        ['label' => '#', 'class' => 'text-center'],
        ['label' => trans('catalogmanagement::product.product_information')],
        ['label' => trans('common.activation')],
        ['label' => trans('common.created_at')],
    ];

    // Add actions column if user has permission
    if (auth()->user()->can('products.bank')) {
        $tableHeaders[] = ['label' => trans('common.actions')];
    }

    // Custom select IDs for filters
    $customSelectIds = [];
    if (auth()->user()->can('products.bank.change-activation')) {
        $customSelectIds[] = 'active';
    }

    // DataTable configuration
    $datatableConfig = [
        'tableId' => 'bankProductsDataTable',
        'ajaxUrl' => route('admin.products.bank.datatable'),
        'headers' => $tableHeaders,
        'customSelectIds' => $customSelectIds,
        'order' => [[3, 'desc']],
        'pageLength' => 10,
        'columnsJson' => 'COLUMNS_DEFINED_IN_SCRIPT',
    ];

    // Filter options
    $filterOptions = [
        'activeStatus' => [
            ['id' => '1', 'name' => __('common.active')],
            ['id' => '2', 'name' => __('common.inactive')]
        ],
    ];
@endphp
