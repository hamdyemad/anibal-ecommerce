@extends('layout.app')

@section('title', 'Income Management')

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
                                <li class="breadcrumb-item active">Income</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">Income Entries</h4>
                    </div>

                    {{-- Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    {{-- Search --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> Search
                                            </label>
                                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search" placeholder="Search orders, vendors...">
                                        </div>
                                    </div>

                                    {{-- From Date --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> From Date
                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date_from">
                                        </div>
                                    </div>

                                    {{-- To Date --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> To Date
                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex align-items-center">
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
                        <table id="incomeDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">Order</span></th>
                                    <th><span class="userDatatable-title">Vendor</span></th>
                                    <th><span class="userDatatable-title">Amount</span></th>
                                    <th><span class="userDatatable-title">Commission</span></th>
                                    <th><span class="userDatatable-title">Vendor Amount</span></th>
                                    <th><span class="userDatatable-title">Date</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <div class="userDatatable__imgWrapper d-flex align-items-center">
                                                <div class="userDatatable__img">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <i class="uil uil-shopping-cart-alt"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="userDatatable-inline-title">
                                                <a href="#" class="text-dark fw-500">
                                                    <h6>#{{ $entry->order->order_number ?? $entry->order_id }}</h6>
                                                </a>
                                                <p class="d-block mb-0">{{ $entry->description }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            {{ $entry->vendor->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            <span class="text-success fw-bold">${{ number_format($entry->amount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            <span class="text-info">${{ number_format($entry->commission_amount, 2) }}</span>
                                            <small class="d-block text-muted">{{ $entry->commission_rate }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            <span class="text-primary fw-bold">${{ number_format($entry->vendor_amount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            {{ $entry->created_at->format('M d, Y') }}
                                            <small class="d-block text-muted">{{ $entry->created_at->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="uil uil-money-dollar-circle display-4 text-muted mb-2"></i>
                                            <p class="text-muted">No income entries found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($entries && $entries->hasPages())
            <div class="col-12">
                <div class="d-flex justify-content-end p-3">
                    {{ $entries->links('vendor.pagination.custom') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
