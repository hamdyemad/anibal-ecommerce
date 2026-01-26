@extends('layout.app')

@php
    // For vendor bank products, vendors should have edit/delete access
    // Set isAdmin to true to show all action buttons
    $isAdmin = true; // Allow vendors to manage their bank products
    
    // But hide admin-only columns (drag handle, vendor column)
    $showAdminColumns = false; // Hide drag handle and vendor column for vendors
    
    // Build table headers (no drag handle for vendors, no vendor column)
    $tableHeaders = [];
    
    $tableHeaders[] = ['label' => '<input type="checkbox" id="selectAllProducts" class="form-check-input" style="cursor: pointer;">', 'class' => 'text-center', 'style' => 'width: 40px;', 'raw' => true];
    $tableHeaders[] = ['label' => '#', 'class' => 'text-center'];
    $tableHeaders[] = ['label' => trans('catalogmanagement::product.product_information')];
    $tableHeaders[] = ['label' => trans('catalogmanagement::product.approval_status'), 'class' => 'text-center'];
    $tableHeaders[] = ['label' => trans('common.activation'), 'class' => 'text-center'];
    $tableHeaders[] = ['label' => trans('common.actions'), 'class' => 'text-center'];

    // Custom select IDs for filters (exclude product_type since it's always bank)
    $customSelectIds = [
        'per_page_filter',
        'brand_filter',
        'department_filter',
        'category_filter',
        'configuration_filter',
        'active',
        'stock_filter',
        'status'
    ];

    // DataTable configuration
    $datatableConfig = [
        'tableId' => 'productsDataTable',
        'ajaxUrl' => route('admin.products.vendor-bank.datatable'),
        'headers' => $tableHeaders,
        'customSelectIds' => $customSelectIds,
        'order' => [[1, 'desc']],
        'pageLength' => 10,
        'columnsJson' => 'COLUMNS_DEFINED_IN_SCRIPT',
        'additionalAjaxData' => null,
    ];

    // Page title
    $pageTitle = trans('catalogmanagement::product.vendor_bank_products');

    // Breadcrumb items
    $breadcrumbItems = [
        [
            'title' => trans('dashboard.title'),
            'url' => route('admin.dashboard'),
            'icon' => 'uil uil-estate',
        ],
        ['title' => $pageTitle],
    ];

    // Filter options (exclude product_type)
    $filterOptions = [
        'perPage' => [
            ['id' => '10', 'name' => '10'],
            ['id' => '25', 'name' => '25'],
            ['id' => '50', 'name' => '50'],
            ['id' => '100', 'name' => '100']
        ],
        'configuration' => [
            ['id' => 'simple', 'name' => __('catalogmanagement::product.simple_product') ?? 'Simple Product'],
            ['id' => 'variants', 'name' => __('catalogmanagement::product.variant_product') ?? 'Variant Product']
        ],
        'activeStatus' => [
            ['id' => '1', 'name' => __('common.active')],
            ['id' => '2', 'name' => __('common.inactive')]
        ],
        'stockStatus' => [
            ['id' => 'instock', 'name' => __('dashboard.instock')],
            ['id' => 'outofstock', 'name' => __('dashboard.out_of_stock')]
        ],
        'approvalStatus' => collect(\Modules\CatalogManagement\app\Models\VendorProduct::getStatuses())
            ->map(fn($label, $value) => ['id' => $value, 'name' => $label])
            ->values()
            ->toArray()
    ];
    
    // Flag to hide product type filter
    $hideProductTypeFilter = true;
    
    // Flag to identify this is vendor bank products page (for export)
    $isVendorBankPage = true;
@endphp

@section('title', $pageTitle)

@push('styles')
<style>
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .rotating {
        animation: rotate 1s linear infinite;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb --}}
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="$breadcrumbItems" />
        </div>
    </div>

    {{-- DataTable Wrapper Component --}}
    <x-datatable-wrapper
        :title="$pageTitle"
        icon="uil uil-database"
        :showExport="true"
        :createRoute="auth()->user()->can('products.create') ? route('admin.products.create') : null"
        :createText="trans('catalogmanagement::product.add_product')"
        :tableId="$datatableConfig['tableId']"
        :ajaxUrl="$datatableConfig['ajaxUrl']"
        :headers="$datatableConfig['headers']"
        :columnsJson="$datatableConfig['columnsJson']"
        :customSelectIds="$datatableConfig['customSelectIds']"
        :order="$datatableConfig['order']"
        :pageLength="$datatableConfig['pageLength']"
        :additionalAjaxData="$datatableConfig['additionalAjaxData']"
        :enableSorting="false"
        sortPermission="products.edit">
        
        {{-- Additional Buttons Slot --}}
        <x-slot name="additionalButtons">
            <a href="{{ route('admin.products.vendor-bank.bulk-upload') }}" class="btn btn-success btn-squared shadow-sm px-4">
                <i class="uil uil-upload"></i> {{ trans('catalogmanagement::product.bulk_upload') }}
            </a>
        </x-slot>        {{-- Filters Slot --}}
        <x-slot name="filters">
            @include('catalogmanagement::product.product_configurations_table._filters')
        </x-slot>
    </x-datatable-wrapper>
</div>

{{-- Modals --}}
@include('catalogmanagement::product.product_configurations_table._modals')

@endsection

{{-- Scripts --}}
@push('scripts')
    @include('catalogmanagement::product.product_configurations_table._datatable-scripts')
    @include('catalogmanagement::product.product_configurations_table._custom-handlers')
@endpush
