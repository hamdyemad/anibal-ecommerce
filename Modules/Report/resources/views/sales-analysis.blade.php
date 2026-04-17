@extends('layout.app')

@section('title')
    {{ __('report::report.sales_analysis') }}
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
                    ['title' => __('report::report.sales_analysis')],
                ]" />
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-25">
            <div class="col-lg-4 col-md-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid {{ config('branding.colors.primary') }};">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: {{ config('branding.colors.primary') }};">
                                <span id="total-orders">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.total_orders') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl" style="border-left: 4px solid #28a745;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #28a745;">
                                <span id="total-revenue">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.total_revenue') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="ap-po-details ap-po-details--4 p-25 radius-xl" style="border-left: 4px solid #ffc107;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #ffc107;">
                                <span id="avg-order-value">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.average_order_value') }}</p>
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

        <!-- Charts Row -->
        <div class="row mb-25">
            <div class="col-lg-6">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">{{ __('report::report.orders_by_stage') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="stageChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">{{ __('report::report.daily_sales') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="dailyChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let stageChart, dailyChart;

        $(document).ready(function() {
            initializeCharts();
            loadReport();

            $('#loadReport').on('click', function() {
                loadReport();
            });
        });

        function initializeCharts() {
            const primaryColor = '{{ config('branding.colors.primary') }}';
            
            // Stage Chart
            const ctxStage = document.getElementById('stageChart').getContext('2d');
            stageChart = new Chart(ctxStage, {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: ['#17a2b8', '#ffc107', '#28a745', '#dc3545', '#6c757d', '#fd7e14', '#343a40'],
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
                                font: { size: 12, weight: 'bold' },
                                padding: 15,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
            
            // Daily Chart
            const ctxDaily = document.getElementById('dailyChart').getContext('2d');
            dailyChart = new Chart(ctxDaily, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('report::report.revenue') }}',
                        data: [],
                        backgroundColor: primaryColor + '80',
                        borderColor: primaryColor,
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                font: { size: 13, weight: 'bold' },
                                padding: 15
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { font: { size: 12 } },
                            grid: { color: '#f0f0f0' }
                        },
                        x: {
                            ticks: { font: { size: 12 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        function loadReport() {
            const from = $('#from-date').val();
            const to = $('#to-date').val();
            
            $.ajax({
                url: '{{ route('admin.reports.sales-analysis.data') }}',
                type: 'GET',
                data: { from: from, to: to },
                success: function(response) {
                    if (response.status && response.data) {
                        const data = response.data;
                        
                        // Update KPIs
                        $('#total-orders').text(data.kpis.total_orders);
                        $('#total-revenue').text(data.kpis.total_revenue.toFixed(2));
                        $('#avg-order-value').text(data.kpis.average_order_value.toFixed(2));
                        
                        // Update Stage Chart
                        stageChart.data.labels = Object.keys(data.orders_by_stage);
                        stageChart.data.datasets[0].data = Object.values(data.orders_by_stage);
                        stageChart.update();
                        
                        // Update Daily Chart
                        const dates = Object.keys(data.daily_sales);
                        const revenues = dates.map(d => data.daily_sales[d].revenue);
                        dailyChart.data.labels = dates;
                        dailyChart.data.datasets[0].data = revenues;
                        dailyChart.update();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading report:', error);
                }
            });
        }
    </script>
@endpush
