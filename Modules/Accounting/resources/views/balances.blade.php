@extends('layout.app')

@section('title', 'Vendor Balances')

@section('content')
<div class="crm mb-25">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="uil uil-estate"></i>Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.accounting.summary') }}">Accounting</a></li>
                                <li class="breadcrumb-item active">Vendor Balances</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">Vendor Balance Overview</h4>
                    </div>

                    {{-- Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    {{-- Search --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> Search
                                            </label>
                                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search" placeholder="Search vendors...">
                                        </div>
                                    </div>

                                    {{-- Min Balance --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="min_balance" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-money-dollar-circle me-1"></i> Min Balance
                                            </label>
                                            <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="min_balance" placeholder="0.00" step="0.01">
                                        </div>
                                    </div>

                                    <div class="col-md-4 d-flex align-items-center">
                                        <button type="button" id="searchBtn" class="btn btn-success btn-default btn-squared me-1">
                                            <i class="uil uil-search me-1"></i> Search
                                        </button>
                                        <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared">
                                            <i class="uil uil-redo me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="balancesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">Vendor</span></th>
                                    <th><span class="userDatatable-title">Total Earnings</span></th>
                                    <th><span class="userDatatable-title">Commission Deducted</span></th>
                                    <th><span class="userDatatable-title">Available Balance</span></th>
                                    <th><span class="userDatatable-title">Withdrawn</span></th>
                                    <th><span class="userDatatable-title">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($balances as $balance)
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <div class="userDatatable__imgWrapper d-flex align-items-center">
                                                <div class="userDatatable__img">
                                                    @if($balance->vendor->logo)
                                                        <img src="{{ asset($balance->vendor->logo) }}" alt="{{ $balance->vendor->name }}" class="rounded-circle">
                                                    @else
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <i class="uil uil-store"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="userDatatable-inline-title">
                                                <h6 class="text-dark fw-500">{{ $balance->vendor->name }}</h6>
                                                <p class="d-block mb-0 text-muted">{{ $balance->vendor->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            <span class="text-success fw-bold">${{ number_format($balance->total_earnings, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            <span class="text-warning fw-bold">${{ number_format($balance->commission_deducted, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            <span class="text-primary fw-bold">${{ number_format($balance->available_balance, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            <span class="text-info fw-bold">${{ number_format($balance->withdrawn_amount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                            <a href="#" class="view btn btn-primary table_action_father" title="View Details">
                                                <i class="uil uil-eye table_action_icon"></i>
                                            </a>
                                            <a href="#" class="edit btn btn-info table_action_father" title="Process Withdrawal">
                                                <i class="uil uil-money-withdrawal table_action_icon"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="uil uil-wallet display-4 text-muted mb-2"></i>
                                            <p class="text-muted">No vendor balances found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($balances && $balances->hasPages())
            <div class="col-12">
                <div class="d-flex justify-content-end p-3">
                    {{ $balances->links('vendor.pagination.custom') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
