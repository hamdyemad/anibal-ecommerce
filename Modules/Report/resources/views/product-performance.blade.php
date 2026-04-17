@extends('layout.app')

@section('title')
    {{ __('report::report.product_performance') }}
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
                    ['title' => __('report::report.product_performance')],
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
                                <span id="total-products">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.total_products') }}</p>
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
                                <span id="total-quantity">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ __('report::report.total_quantity') }}</p>
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

        <!-- Top Products Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('report::report.top_products') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 table-bordered table-hover">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">{{ __('report::report.product_name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report::report.quantity_sold') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report::report.revenue') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report::report.orders_count') }}</span></th>
                                </tr>
                            </thead>
                            <tbody id="top-products-table">
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('report::report.no_data') }}</td>
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
    <script>
        $(document).ready(function() {
            loadReport();

            $('#loadReport').on('click', function() {
                loadReport();
            });
        });

        function loadReport() {
            const from = $('#from-date').val();
            const to = $('#to-date').val();
            
            $.ajax({
                url: '{{ route('admin.reports.product-performance.data') }}',
                type: 'GET',
                data: { from: from, to: to },
                success: function(response) {
                    if (response.status && response.data) {
                        const data = response.data;
                        
                        // Update KPIs
                        $('#total-products').text(data.kpis.total_products);
                        $('#total-revenue').text(data.kpis.total_revenue.toFixed(2));
                        $('#total-quantity').text(data.kpis.total_quantity);
                        
                        // Update Table
                        const tbody = $('#top-products-table');
                        tbody.empty();
                        
                        if (data.top_products.length > 0) {
                            data.top_products.forEach(product => {
                                const row = `<tr>
                                    <td><strong>${product.product_name}</strong></td>
                                    <td>${product.quantity_sold}</td>
                                    <td>${product.revenue}</td>
                                    <td>${product.orders_count}</td>
                                </tr>`;
                                tbody.append(row);
                            });
                        } else {
                            tbody.html('<tr><td colspan="4" class="text-center">{{ __('report::report.no_data') }}</td></tr>');
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
