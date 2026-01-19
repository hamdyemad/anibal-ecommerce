@extends('layout.app')

@section('title', trans('refund::refund.admin_settings.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => trans('refund::refund.admin_settings.title')]
            ]" />
        </div>
    </div>

    @php
    // Build table headers
    $headers = [
        ['label' => '#', 'class' => 'text-center'],
        ['label' => trans('vendor::vendor.name'), 'class' => 'text-center'],
        ['label' => trans('refund::refund.vendor_settings.vendor_refund_days'), 'class' => 'text-center'],
        ['label' => trans('refund::refund.fields.customer_pays_return_shipping'), 'class' => 'text-center'],
    ];

    // Build columns array - keys must match the data returned from DataTable
    $columns = [
        ['data' => 'index', 'orderable' => false, 'searchable' => false, 'className' => 'text-center fw-bold', 'width' => '5%'],
        ['data' => 'vendor_name', 'orderable' => false, 'className' => 'text-center', 'width' => '40%'],
        ['data' => 'refund_days', 'orderable' => false, 'searchable' => false, 'className' => 'text-center', 'width' => '30%'],
        ['data' => 'return_shipping', 'orderable' => false, 'searchable' => false, 'className' => 'text-center', 'width' => '25%'],
    ];
    @endphp

    {{-- DataTable Wrapper Component --}}
    <x-datatable-wrapper
        :title="trans('refund::refund.admin_settings.title')"
        icon="uil uil-setting"
        tableId="vendorRefundSettingsTable"
        ajaxUrl="{{ route('admin.refunds.admin-settings.datatable') }}"
        :headers="$headers"
        :columnsJson="json_encode($columns)"
        :customSelectIds="[]"
        :order="[[0, 'desc']]"
        :pageLength="10">
        
        {{-- Search & Filters Component --}}
        <x-slot name="filters">
            <x-datatable-filters-advanced
                :searchPlaceholder="trans('common.search')"
                :filters="[]"
                :showDateFilters="false"
            />
        </x-slot>
    </x-datatable-wrapper>
</div>
@endsection

@push('styles')
<style>
    /* Center alignment for table cells */
    #vendorRefundSettingsTable tbody td {
        vertical-align: middle;
        text-align: center;
    }
    
    #vendorRefundSettingsTable thead th {
        text-align: center;
        vertical-align: middle;
    }
    
    /* Vendor name column - center the flex container */
    #vendorRefundSettingsTable tbody td .d-flex {
        justify-content: center;
    }
</style>
@endpush
