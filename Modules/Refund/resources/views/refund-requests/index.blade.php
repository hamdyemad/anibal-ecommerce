@extends('layout.app')

@section('title', trans('menu.refunds.title'))

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb Component --}}
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                [
                    'title' => trans('dashboard.title'),
                    'url' => route('admin.dashboard'),
                    'icon' => 'uil uil-estate',
                ],
                ['title' => trans('menu.refunds.title')],
            ]" />
        </div>
    </div>

    @php
    // Build table headers
    $headers = [
        ['label' => '#', 'class' => 'text-center'],
        ['label' => trans('refund::refund.titles.refund_details')],
        ['label' => trans('refund::refund.fields.status'), 'class' => 'text-center'],
        ['label' => trans('common.actions'), 'class' => 'text-center'],
    ];

    // Build columns array
    $columns = [
        ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false, 'className' => 'text-center fw-bold'],
        ['data' => 'refund_info', 'name' => 'refund_info', 'orderable' => false, 'searchable' => false],
        ['data' => 'status', 'name' => 'status', 'orderable' => false, 'searchable' => false, 'className' => 'text-center'],
        ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'className' => 'text-center'],
    ];
    @endphp

    {{-- DataTable Wrapper Component with Built-in Script --}}
    <x-datatable-wrapper
        :title="trans('menu.refunds.all')"
        icon="uil uil-redo"
        :showExport="false"
        tableId="refundsDataTable"
        ajaxUrl="{{ route('admin.refunds.datatable') }}"
        :headers="$headers"
        :columnsJson="json_encode($columns)"
        :customSelectIds="['status_filter']"
        :order="[[0, 'desc']]"
        :pageLength="10">
        
        {{-- Search & Filters Component --}}
        <x-slot name="filters">
            <x-datatable-filters-advanced
                :searchPlaceholder="trans('refund::refund.fields.refund_number')"
                :filters="[
                    [
                        'name' => 'status_filter',
                        'id' => 'status_filter',
                        'label' => trans('refund::refund.fields.status'),
                        'icon' => 'uil uil-check-circle',
                        'options' => collect(\Modules\Refund\app\Models\RefundRequest::STATUSES)->map(fn($label, $value) => ['id' => $value, 'name' => trans('refund::refund.statuses.' . $value)])->values()->toArray(),
                        'selected' => request('status'),
                        'placeholder' => __('common.all'),
                    ],
                ]"
                :showDateFilters="true"
            />
        </x-slot>
    </x-datatable-wrapper>
</div>
@endsection
