@extends('layout.app')

@section('title', __('accounting.accounting_summary'))

@section('content')
<style>
    .ap-po-details__titlebar h1 {
        font-weight: bold;
        color: var(--color-primary);
    }

    .ap-po-details__titlebar p {
        font-weight: bold !important;
    }

    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .metric-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }

    .metric-item:last-child {
        border-bottom: none;
    }

    .metric-label {
        font-weight: 600;
        color: #666;
    }

    .metric-value {
        font-weight: bold;
        font-size: 1.1em;
    }

    .positive { color: #28a745; }
    .negative { color: #dc3545; }
    .neutral { color: #6c757d; }
</style>

<div class="crm mb-25">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="uil uil-estate"></i>{{ __('accounting.dashboard') }}</a></li>
                                <li class="breadcrumb-item active">{{ __('accounting.accounting_summary') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Main Stats Cards --}}
            <div class="col-12">
                {{-- Filters --}}
                <div class="mb-25">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                {{-- From Date --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_from" class="il-gray fs-14 fw-500 mb-10">
                                            <i class="uil uil-calendar-alt me-1"></i> {{ __('accounting.date_from') }}
                                        </label>
                                        <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date_from">
                                    </div>
                                </div>

                                {{-- To Date --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_to" class="il-gray fs-14 fw-500 mb-10">
                                            <i class="uil uil-calendar-alt me-1"></i> {{ __('accounting.date_to') }}
                                        </label>
                                        <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date_to">
                                    </div>
                                </div>

                                <div class="col-md-4 d-flex align-items-center">
                                    <button type="button" id="filterBtn" class="btn btn-success btn-default btn-squared me-1">
                                        <i class="uil uil-filter me-1"></i> {{ __('accounting.filter') }}
                                    </button>
                                    <button type="button" id="resetBtn" class="btn btn-warning btn-default btn-squared">
                                        <i class="uil uil-redo me-1"></i> {{ __('accounting.reset') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Total Income --}}
                    <div class="col-12 col-md-3 mb-25">
                        <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                            <div class="overview-content w-100">
                                <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                    <div class="ap-po-details__titlebar">
                                        <h1 id="total-income">{{ number_format($summary['total_income'], 2) }} {{ currency() }}</h1>
                                        <p>{{ __('accounting.total_income') }}</p>
                                    </div>
                                    <div class="ap-po-details__icon-area">
                                        <div class="svg-icon order-bg-opacity-primary color-primary">
                                            <i class="uil uil-money-dollar-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Expenses --}}
                    <div class="col-12 col-md-3 mb-25">
                        <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                            <div class="overview-content w-100">
                                <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                    <div class="ap-po-details__titlebar">
                                        <h1 id="total-expenses">{{ number_format($summary['total_expenses'], 2) }} {{ currency() }}</h1>
                                        <p>{{ __('accounting.total_expenses') }}</p>
                                    </div>
                                    <div class="ap-po-details__icon-area">
                                        <div class="svg-icon order-bg-opacity-danger color-danger">
                                            <i class="uil uil-shopping-cart"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Commissions --}}
                    <div class="col-12 col-md-3 mb-25">
                        <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                            <div class="overview-content w-100">
                                <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                    <div class="ap-po-details__titlebar">
                                        <h1 id="total-commissions">{{ number_format($summary['total_commissions'], 2) }} {{ currency() }}</h1>
                                        <p>{{ __('accounting.total_commissions') }}</p>
                                    </div>
                                    <div class="ap-po-details__icon-area">
                                        <div class="svg-icon order-bg-opacity-info color-info">
                                            <i class="uil uil-percent"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Net Profit --}}
                    <div class="col-12 col-md-3 mb-25">
                        <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                            <div class="overview-content w-100">
                                <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                    <div class="ap-po-details__titlebar">
                                        <h1 id="net-profit">{{ number_format($summary['net_profit'], 2) }} {{ currency() }}</h1>
                                        <p>{{ __('accounting.net_profit') }}</p>
                                    </div>
                                    <div class="ap-po-details__icon-area">
                                        <div class="svg-icon order-bg-opacity-success color-success">
                                            <i class="uil uil-line-chart"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- New Detailed Metrics Section --}}
            <div class="col-lg-8">
                <div class="chart-container">
                    <h5 class="mb-4">{{ __('accounting.financial_breakdown') }}</h5>

                    <div class="metric-item">
                        <span class="metric-label">{{ __('accounting.gross_revenue') }}</span>
                        <span class="metric-value positive" id="gross-revenue">{{ number_format($summary['total_income'], 2) }} {{ currency() }}</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">{{ __('accounting.operating_expenses') }}</span>
                        <span class="metric-value negative" id="operating-expenses">{{ number_format($summary['total_expenses'], 2) }} {{ currency() }}</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">{{ __('accounting.commission_earned') }}</span>
                        <span class="metric-value positive" id="commission-earned">{{ number_format($summary['total_commissions'], 2) }} {{ currency() }}</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">{{ __('accounting.vendor_payouts') }}</span>
                        <span class="metric-value neutral" id="vendor-payouts">{{ number_format($summary['total_income'] - $summary['total_commissions'], 2) }} {{ currency() }}</span>
                    </div>

                    <div class="metric-item">
                        <span class="metric-label">{{ __('accounting.profit_margin') }}</span>
                        <span class="metric-value {{ $summary['total_income'] > 0 ? ($summary['net_profit'] > 0 ? 'positive' : 'negative') : 'neutral' }}" id="profit-margin">
                            {{ $summary['total_income'] > 0 ? number_format(($summary['net_profit'] / $summary['total_income']) * 100, 1) : '0.0' }}%
                        </span>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="col-lg-4">
                <div class="chart-container">
                    <h5 class="mb-4">{{ __('accounting.quick_insights') }}</h5>

                    <div class="text-center mb-4">
                        <div class="display-6 fw-bold {{ $summary['total_refunds'] > 0 ? 'text-warning' : 'text-success' }}">
                            {{ number_format($summary['total_refunds'], 2) }} {{ currency() }}
                        </div>
                        <small class="text-muted">{{ __('accounting.total_refunds') }}</small>
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 mb-1 text-primary">{{ $summary['total_income'] > 0 ? number_format(($summary['total_commissions'] / $summary['total_income']) * 100, 1) : '0.0' }}%</div>
                                <small class="text-muted">{{ __('accounting.avg_commission_rate') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-1 text-info">{{ $summary['total_expenses'] > 0 ? number_format(($summary['total_expenses'] / ($summary['total_income'] ?: 1)) * 100, 1) : '0.0' }}%</div>
                            <small class="text-muted">{{ __('accounting.expense_ratio') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
