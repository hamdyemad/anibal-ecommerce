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

    {{-- Refund Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">{{ trans('refund::refund.statistics.title') }}</h5>
        </div>
        
        {{-- Loop through all statuses and create cards --}}
        @foreach($statistics['status_data'] as $status => $data)
            @php
            $config = \Modules\Refund\app\Models\RefundRequest::getStatusConfig($status);
            @endphp
            <div class="col-xl-4 col-md-6 mb-2">
                <div class="card card-h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted mb-2 lh-1 d-block text-truncate">{{ trans('refund::refund.statuses.' . $status) }}</span>
                                <h4 class="mb-2">
                                    <span class="counter-value" data-target="{{ $data['count'] }}">{{ $data['count'] }}</span>
                                </h4>
                                <p class="text-muted mb-0">
                                    <strong>{{ $data['amount_formatted'] }}</strong> {{ trans('common.currency') }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 text-end dash-widget">
                                <div class="avatar-sm rounded-circle bg-soft-{{ $config['color'] }} align-self-center mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-soft-{{ $config['color'] }}">
                                        <i class="uil {{ $config['icon'] }} font-size-24 text-{{ $config['color'] }}"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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

{{-- Include Refund Actions Component (for modals and JS helper) --}}
<x-refund::refund-actions />
@endsection
