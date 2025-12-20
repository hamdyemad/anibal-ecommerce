@extends('layout.app')

@section('title')
    {{ trans('menu.reports.orders report') }}
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('menu.reports.title'),
                        'url' => route('admin.reports.index'),
                    ],
                    ['title' => trans('menu.reports.orders report')],
                ]" />
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-25">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid {{ config('branding.colors.primary') }};">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: {{ config('branding.colors.primary') }};">
                                <span id="record-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.orders_in_report') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl" style="border-left: 4px solid {{ config('branding.colors.secondary') }};">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: {{ config('branding.colors.secondary') }};">
                                <span id="total-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.total_orders') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--4 p-25 radius-xl" style="border-left: 4px solid #28a745;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #28a745;">
                                <span id="completed-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.completed_orders') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid #ffc107;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #ffc107;">
                                <span id="pending-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.pending_orders') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-25">
            <div class="col-lg-8">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">
                            {{ trans('report.orders_trend') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="ordersChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">{{ trans('report.order_status_distribution') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="statusChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('menu.reports.orders report') }}</h4>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    {{-- Search --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ trans('report.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="{{ trans('report.search') }}..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- From Date --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="from-date" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('report.from_date') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="from-date">
                                        </div>
                                    </div>

                                    {{-- To Date --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="to-date" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('report.to_date') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="to-date">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status-filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ trans('report.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status-filter">
                                                <option value="">{{ trans('report.all_status') }}</option>
                                                <option value="pending">{{ trans('report.pending') }}</option>
                                                <option value="confirmed">{{ trans('report.confirmed') }}</option>
                                                <option value="delivered">{{ trans('report.delivered') }}</option>
                                                <option value="cancelled">{{ trans('report.cancelled') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center gap-2">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ trans('report.search_button') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ trans('report.search_button') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ trans('report.reset_button') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ trans('report.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <label class="ms-2 mb-0">{{ trans('report.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="orders-table" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.order_number') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.customer_name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.total') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.order_date') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Table styling */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(90deg, {{ config('branding.colors.primary') }} 0%, {{ config('branding.colors.secondary') }} 100%);
            border: none;
            padding: 1rem 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.6px;
            color: white;
        }

        .table tbody td {
            padding: 0.9rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.9rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            background-color: #fff;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff !important;
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.05);
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table tbody tr:nth-child(even):hover {
            background-color: #f8f9ff !important;
        }

        /* DataTables wrapper styling */
        .dataTables_wrapper {
            padding: 0;
        }

        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 1.5rem;
            padding: 0 25px;
            padding-top: 20px;
        }

        .dataTables_paginate .paginate_button {
            display: inline-block;
            padding: 0.5rem 0.8rem;
            margin: 0 0.25rem;
            border: 1px solid #dee2e6;
            background-color: #fff;
            color: #495057;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1;
        }

        .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background-color: {{ config('branding.colors.primary') }};
            border-color: {{ config('branding.colors.primary') }};
            color: white;
        }

        .dataTables_paginate .paginate_button.active {
            background-color: {{ config('branding.colors.primary') }} !important;
            border-color: {{ config('branding.colors.primary') }} !important;
            color: white !important;
        }

        /* Form label styling */
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table thead th {
                padding: 0.75rem 0.5rem;
                font-size: 0.7rem;
            }

            .table tbody td {
                padding: 0.65rem 0.5rem;
                font-size: 0.85rem;
            }
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let ordersChart, statusChart;

        $(document).ready(function() {
            let per_page = 10;

            // Initialize DataTable
            let table = $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.data.orders') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.from_date = $('#from-date').val();
                        d.to_date = $('#to-date').val();
                        d.status = $('#status-filter').val();
                        return d;
                    },
                    dataFilter: function(data) {
                        let json = JSON.parse(data);
                        if (json.status && json.data) {
                            json.recordsTotal = json.data.total || 0;
                            json.recordsFiltered = json.data.count || 0;
                            json.pending = json.data.statistics?.pending || 0;
                            json.completed = json.data.statistics?.completed || 0;
                            json.orders_trend = json.data.orders_trend || {};
                            json.status_distribution = json.data.status_distribution || {};
                            json.data = json.data.data || [];
                        }
                        return JSON.stringify(json);
                    },
                    dataSrc: function(json) {
                        if (!json.status) return [];
                        $('#record-count').text(json.recordsFiltered || 0);
                        $('#total-count').text(json.recordsTotal || 0);
                        $('#completed-count').text(json.completed || 0);
                        $('#pending-count').text(json.pending || 0);
                        updateChartsWithData(json.status_distribution || {}, json.orders_trend || {});
                        return json.data || [];
                    }
                },
                columns: [
                    {
                        data: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'order_number',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="badge bg-light text-dark">#' + (data || '') + '</span>';
                        }
                    },
                    {
                        data: 'customer_name',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<strong>' + (data || '--') + '</strong>';
                        }
                    },
                    {
                        data: 'status',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            const statusColors = {
                                'pending': 'warning',
                                'confirmed': 'info',
                                'processing': 'primary',
                                'delivered': 'success',
                                'cancelled': 'danger',
                                'refunded': 'secondary'
                            };
                            const color = statusColors[data] || 'secondary';
                            return '<span class="badge bg-' + color + '">' + (data ? data.charAt(0).toUpperCase() + data.slice(1) : 'N/A') + '</span>';
                        }
                    },
                    {
                        data: 'total',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ? parseFloat(data).toFixed(2) : '0.00';
                        }
                    },
                    {
                        data: 'created_at',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (!data) return '--';
                            const date = new Date(data);
                            return date.toLocaleDateString();
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' + '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#from-date, #to-date, #status-filter').on('change', function() {
                table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#search, #from-date, #to-date, #status-filter').val('');
                table.ajax.reload();
            });

            initializeCharts();
        });

        function initializeCharts() {
            const primaryColor = '{{ config('branding.colors.primary') }}';
            const secondaryColor = '{{ config('branding.colors.secondary') }}';

            // Orders Trend Chart
            const ctxOrders = document.getElementById('ordersChart').getContext('2d');
            ordersChart = new Chart(ctxOrders, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Orders',
                        data: [],
                        borderColor: primaryColor,
                        backgroundColor: primaryColor + '15',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: primaryColor,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, labels: { font: { size: 13, weight: 'bold' } } }
                    },
                    scales: {
                        y: { beginAtZero: true },
                        x: { ticks: { font: { size: 12 } } }
                    }
                }
            });

            // Status Distribution Chart
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            statusChart = new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Confirmed', 'Delivered', 'Cancelled'],
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: [primaryColor, secondaryColor, '#28a745', '#dc3545'],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'bottom', labels: { font: { size: 12, weight: 'bold' } } }
                    }
                }
            });
        }

        function updateChartsWithData(statusDistribution, ordersTrend) {
            const dates = Object.keys(ordersTrend).sort();
            ordersChart.data.labels = dates;
            ordersChart.data.datasets[0].data = dates.map(date => ordersTrend[date]);
            ordersChart.update();

            statusChart.data.datasets[0].data = [
                statusDistribution['pending'] || 0,
                statusDistribution['confirmed'] || 0,
                statusDistribution['delivered'] || 0,
                statusDistribution['cancelled'] || 0
            ];
            statusChart.update();
        }
    </script>
@endpush
