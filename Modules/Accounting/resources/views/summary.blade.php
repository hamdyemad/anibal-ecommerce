@extends('layout.app')

@section('title', 'Accounting Summary')

@section('content')
<style>
    .ap-po-details__titlebar h1 {
        font-weight: bold;
        color: var(--color-primary);
    }

    .ap-po-details__titlebar p {
        font-weight: bold !important;
    }

    .stat-card-link {
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        text-decoration: none;
        display: block;
    }

    .stat-card-link:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-card-link:hover .ap-po-details {
        border-color: var(--color-primary);
    }
</style>

<div class="crm mb-25">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="uil uil-estate"></i>Dashboard</a></li>
                                <li class="breadcrumb-item active">Accounting Summary</li>
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
                                            <i class="uil uil-calendar-alt me-1"></i> From Date
                                        </label>
                                        <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date_from">
                                    </div>
                                </div>

                                {{-- To Date --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_to" class="il-gray fs-14 fw-500 mb-10">
                                            <i class="uil uil-calendar-alt me-1"></i> To Date
                                        </label>
                                        <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date_to">
                                    </div>
                                </div>

                                <div class="col-md-4 d-flex align-items-center">
                                    <button type="button" id="filterBtn" class="btn btn-success btn-default btn-squared me-1">
                                        <i class="uil uil-filter me-1"></i> Filter
                                    </button>
                                    <button type="button" id="resetBtn" class="btn btn-warning btn-default btn-squared">
                                        <i class="uil uil-redo me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Total Income --}}
                    <div class="col-12 col-md-3 mb-25">
                        <a href="{{ route('admin.accounting.income') }}" class="stat-card-link">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1>${{ number_format($summary['total_income'], 2) }}</h1>
                                            <p>Total Income</p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-primary color-primary">
                                                <i class="uil uil-money-dollar-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- Total Expenses --}}
                    <div class="col-12 col-md-3 mb-25">
                        <a href="{{ route('admin.accounting.expenses') }}" class="stat-card-link">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1>${{ number_format($summary['total_expenses'], 2) }}</h1>
                                            <p>Total Expenses</p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-danger color-danger">
                                                <i class="uil uil-shopping-cart"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- Total Commissions --}}
                    <div class="col-12 col-md-3 mb-25">
                        <a href="{{ route('admin.accounting.balances') }}" class="stat-card-link">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1>${{ number_format($summary['total_commissions'], 2) }}</h1>
                                            <p>Total Commissions</p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-info color-info">
                                                <i class="uil uil-percent"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- Net Profit --}}
                    <div class="col-12 col-md-3 mb-25">
                        <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                            <div class="overview-content w-100">
                                <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                    <div class="ap-po-details__titlebar">
                                        <h1>${{ number_format($summary['net_profit'], 2) }}</h1>
                                        <p>Net Profit</p>
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

            {{-- Quick Actions --}}
            <div class="col-12">
                <div class="row">
                    {{-- Income --}}
                    <div class="col-12 col-md-4 mb-25">
                        <a href="{{ route('admin.accounting.income') }}" class="stat-card-link">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1><i class="uil uil-money-dollar-circle"></i></h1>
                                            <p>Income Management</p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-success color-success">
                                                <i class="uil uil-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- Expenses --}}
                    <div class="col-12 col-md-4 mb-25">
                        <a href="{{ route('admin.accounting.expenses') }}" class="stat-card-link">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1><i class="uil uil-shopping-cart"></i></h1>
                                            <p>Expense Management</p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-danger color-danger">
                                                <i class="uil uil-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- Expense Items --}}
                    <div class="col-12 col-md-4 mb-25">
                        <a href="{{ route('admin.accounting.expense-items') }}" class="stat-card-link">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1><i class="uil uil-list-check"></i></h1>
                                            <p>Expense Categories</p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-warning color-warning">
                                                <i class="uil uil-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- Vendor Balances --}}
                    <div class="col-12 col-md-4 mb-25">
                        <a href="{{ route('admin.accounting.balances') }}" class="stat-card-link">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1><i class="uil uil-wallet"></i></h1>
                                            <p>Vendor Balances</p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-info color-info">
                                                <i class="uil uil-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
