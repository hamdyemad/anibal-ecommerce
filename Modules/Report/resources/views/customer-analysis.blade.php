@extends('layout.app')

@section('title')
    {{ __('report::report.customer_analysis') }}
@endsection

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
                    ['title' => __('report::report.customer_analysis')],
                ]" />
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-25">
            <div class="col-lg-3 col-md-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid {{ config('branding.colors.primary') }};">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: {{ config('branding.colors.primary') }};">
                                <span id="total-customers">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.total_customers') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl" style="border-left: 4px solid #28a745;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #28a745;">
                                <span id="new-customers">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.new_customers') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ap-po-details ap-po-details--4 p-25 radius-xl" style="border-left: 4px solid #ffc107;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #ffc107;">
                                <span id="returning-customers">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.returning_customers') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid #17a2b8;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #17a2b8;">
                                <span id="avg-customer-value">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.average_customer_value') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Row -->
        <div class="row mb-25">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="from-date" class="il-gray fs-14 fw-500 mb-10">
                                        <i class="uil uil-calendar-alt me-1"></i>
                                        {{ __('report::report.from_date') }}
                                    </label>
                                    <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                        id="from-date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="to-date" class="il-gray fs-14 fw-500 mb-10">
                                        <i class="uil uil-calendar-alt me-1"></i>
                                        {{ __('report::report.to_date') }}
                                    </label>
                                    <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                        id="to-date" value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" id="loadReport" class="btn btn-success btn-default btn-squared w-100">
                                    <i class="uil uil-chart-line me-1"></i>
                                    {{ __('report::report.load_report') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Table Row -->
        <div class="row mb-25">
            <div class="col-lg-6">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">{{ __('report::report.customer_distribution') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="customerChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl h-100">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('report::report.top_customers') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 table-bordered table-hover">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">{{ __('report::report.customer_name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report::report.orders_count') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report::report.total_spent') }}</span></th>
                                </tr>
                            </thead>
                            <tbody id="top-customers-table">
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('report::report.no_data') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
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

        .table tbody tr:hover {
            background-color: #f8f9ff !important;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let customerChart;

        $(document).ready(function() {
            initializeChart();
            loadReport();

            $('#loadReport').on('click', function() {
                loadReport();
            });
        });

        function initializeChart() {
            const ctx = document.getElementById('customerChart').getContext('2d');
            customerChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['{{ __('report::report.new_customers') }}', '{{ __('report::report.returning_customers') }}'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#28a745', '#ffc107'],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 13, weight: 'bold' },
                                padding: 15,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        function loadReport() {
            const from = $('#from-date').val();
            const to = $('#to-date').val();
            
            $.ajax({
                url: '{{ route('admin.reports.customer-analysis.data') }}',
                type: 'GET',
                data: { from: from, to: to },
                success: function(response) {
                    if (response.status && response.data) {
                        const data = response.data;
                        
                        // Update KPIs
                        $('#total-customers').text(data.kpis.total_customers);
                        $('#new-customers').text(data.kpis.new_customers);
                        $('#returning-customers').text(data.kpis.returning_customers);
                        $('#avg-customer-value').text(data.kpis.average_customer_value.toFixed(2));
                        
                        // Update Chart
                        customerChart.data.datasets[0].data = [
                            data.customer_distribution.new,
                            data.customer_distribution.returning
                        ];
                        customerChart.update();
                        
                        // Update Table
                        const tbody = $('#top-customers-table');
                        tbody.empty();
                        
                        if (data.top_customers.length > 0) {
                            data.top_customers.forEach(customer => {
                                const row = `<tr>
                                    <td><strong>${customer.customer_name}</strong></td>
                                    <td>${customer.orders_count}</td>
                                    <td>${customer.total_spent}</td>
                                </tr>`;
                                tbody.append(row);
                            });
                        } else {
                            tbody.html('<tr><td colspan="3" class="text-center">{{ __('report::report.no_data') }}</td></tr>');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading report:', error);
                }
            });
        }
    </script>
@endpush
