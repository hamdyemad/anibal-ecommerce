@extends('layout.app')

@section('title')
    {{ __('report::report.profitability_report') }}
@endsection

@push('styles')
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
                    ['title' => __('report::report.profitability_report')],
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
                                <span id="total-revenue">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.total_revenue') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl" style="border-left: 4px solid #dc3545;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #dc3545;">
                                <span id="total-costs">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.total_costs') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--4 p-25 radius-xl" style="border-left: 4px solid #28a745;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #28a745;">
                                <span id="net-profit">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.net_profit') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid #ffc107;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #ffc107;">
                                <span id="profit-margin">0%</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.profit_margin') }}</p>
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
                                    <input type="date"
                                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                        id="from-date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="to-date" class="il-gray fs-14 fw-500 mb-10">
                                        <i class="uil uil-calendar-alt me-1"></i>
                                        {{ __('report::report.to_date') }}
                                    </label>
                                    <input type="date"
                                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                        id="to-date" value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" id="loadReport"
                                    class="btn btn-success btn-default btn-squared w-100">
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
            <div class="col-lg-12">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">
                            {{ __('report::report.monthly_trend') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 400px; display: flex; align-items: center;">
                        <canvas id="profitChart" style="max-height: 350px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let profitChart;

        $(document).ready(function() {
            initializeChart();
            loadReport();

            $('#loadReport').on('click', function() {
                loadReport();
            });
        });

        function initializeChart() {
            const primaryColor = '{{ config('branding.colors.primary') }}';
            const ctx = document.getElementById('profitChart').getContext('2d');
            
            profitChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: '{{ __('report::report.revenue') }}',
                            data: [],
                            borderColor: '#28a745',
                            backgroundColor: '#28a74520',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: '{{ __('report::report.costs') }}',
                            data: [],
                            borderColor: '#dc3545',
                            backgroundColor: '#dc354520',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: '{{ __('report::report.profit') }}',
                            data: [],
                            borderColor: primaryColor,
                            backgroundColor: primaryColor + '20',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                font: { size: 13, weight: 'bold' },
                                padding: 15,
                                usePointStyle: true
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
                url: '{{ route('admin.reports.profitability.data') }}',
                type: 'GET',
                data: { from: from, to: to },
                success: function(response) {
                    if (response.status && response.data) {
                        const data = response.data;
                        
                        // Update KPIs
                        $('#total-revenue').text(data.kpis.total_revenue.toFixed(2));
                        $('#total-costs').text(data.kpis.total_costs.toFixed(2));
                        $('#net-profit').text(data.kpis.net_profit.toFixed(2));
                        $('#profit-margin').text(data.kpis.profit_margin.toFixed(2) + '%');
                        
                        // Update Chart
                        const labels = data.monthly_trend.map(m => m.month);
                        const revenues = data.monthly_trend.map(m => m.revenue);
                        const costs = data.monthly_trend.map(m => m.costs);
                        const profits = data.monthly_trend.map(m => m.profit);
                        
                        profitChart.data.labels = labels;
                        profitChart.data.datasets[0].data = revenues;
                        profitChart.data.datasets[1].data = costs;
                        profitChart.data.datasets[2].data = profits;
                        profitChart.update();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading report:', error);
                }
            });
        }
    </script>
@endpush
