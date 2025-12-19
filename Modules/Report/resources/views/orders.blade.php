@extends('layout.app')

@section('title')
    {{ trans('menu.reports.orders report') }}
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
                    ['title' => trans('menu.reports.orders report')],
                ]" />
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-25">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid #28a745;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #28a745;">
                                {{ isset($data) ? count($data['data']) : 0 }}
                            </h1>
                            <p class="ap-po-details__text">{{ __('Orders in Report') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl" style="border-left: 4px solid #17a2b8;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #17a2b8;">
                                {{ isset($data) ? $data['total'] : 0 }}
                            </h1>
                            <p class="ap-po-details__text">{{ __('Total Orders') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--4 p-25 radius-xl" style="border-left: 4px solid {{ config('branding.colors.primary') }};">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: {{ config('branding.colors.primary') }};">
                                {{ isset($data) ? $data['last_page'] : 0 }}
                            </h1>
                            <p class="ap-po-details__text">{{ __('Total Pages') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid #ffc107;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #ffc107;">
                                {{ isset($data) ? $data['current_page'] : 1 }}
                            </h1>
                            <p class="ap-po-details__text">{{ __('Current Page') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-25">
            <div class="col-lg-12">
                <div class="card radius-xl p-25">
                    <h5 class="mb-20" style="color: #28a745;">
                        <i class="uil uil-filter me-2"></i>{{ __('Filters') }}
                    </h5>
                    <form method="GET" action="{{ route('admin.reports.orders') }}" class="form-inline gap-2">
                        <div class="form-group flex-fill">
                            <label class="form-label">{{ __('From Date') }}</label>
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}" style="border-left: 3px solid #28a745;">
                        </div>
                        <div class="form-group flex-fill">
                            <label class="form-label">{{ __('To Date') }}</label>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}" style="border-left: 3px solid #28a745;">
                        </div>
                        <div class="form-group flex-fill">
                            <label class="form-label">{{ __('Type/Status') }}</label>
                            <input type="text" name="type" class="form-control" placeholder="{{ __('Order type') }}" value="{{ request('type') }}" style="border-left: 3px solid #28a745;">
                        </div>
                        <div class="form-group flex-fill">
                            <label class="form-label">{{ __('Search') }}</label>
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Order #, Customer') }}" value="{{ request('search') }}" style="border-left: 3px solid #28a745;">
                        </div>
                        <div class="form-group d-flex align-items-end">
                            <button type="submit" class="btn btn-success">
                                <i class="uil uil-search me-1"></i>{{ __('Filter') }}
                            </button>
                            <a href="{{ route('admin.reports.orders') }}" class="btn btn-secondary ms-2">
                                <i class="uil uil-redo me-1"></i>{{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card radius-xl">
                    <div class="card__header p-25">
                        <h5 style="color: #28a745;">{{ trans('menu.reports.orders report') }}</h5>
                    </div>
                    <div class="card__body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: #f8f9fa; border-bottom: 2px solid #28a745;">
                                    <tr>
                                        <th class="fw-bold" style="color: #28a745;">{{ __('Order ID') }}</th>
                                        <th class="fw-bold" style="color: #28a745;">{{ __('Customer') }}</th>
                                        <th class="fw-bold" style="color: #28a745;">{{ __('Order #') }}</th>
                                        <th class="fw-bold" style="color: #28a745;">{{ __('Type') }}</th>
                                        <th class="fw-bold" style="color: #28a745;">{{ __('Status') }}</th>
                                        <th class="fw-bold" style="color: #28a745;">{{ __('Order Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(isset($data) ? $data['data'] : [] as $order)
                                        <tr class="hover-row">
                                            <td>
                                                <span class="badge bg-success">
                                                    #{{ $order->id }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $order->customer?->first_name }} {{ $order->customer?->last_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $order->order_number ?? '--' }}</span>
                                            </td>
                                            <td>{{ ucfirst($order->type ?? 'N/A') }}</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'confirmed' => 'info',
                                                        'processing' => 'primary',
                                                        'shipped' => 'primary',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger',
                                                        'refunded' => 'secondary',
                                                    ];
                                                    $statusColor = $statusColors[$order->status ?? 'pending'] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst($order->status ?? 'pending') }}</span>
                                            </td>
                                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center p-4 text-muted">
                                                <i class="uil uil-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                                <p class="mt-2">{{ __('No data found') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if(isset($data) && $data['last_page'] > 1)
                        <div class="card__footer p-25">
                            <nav aria-label="Page navigation">
                                <ul class="pagination mb-0">
                                    @if($data['current_page'] > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ route('admin.reports.orders', array_merge(request()->query(), ['page' => 1])) }}">
                                                {{ __('First') }}
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="{{ route('admin.reports.orders', array_merge(request()->query(), ['page' => $data['current_page'] - 1])) }}">
                                                {{ __('Previous') }}
                                            </a>
                                        </li>
                                    @endif

                                    @for($i = max(1, $data['current_page'] - 2); $i <= min($data['last_page'], $data['current_page'] + 2); $i++)
                                        <li class="page-item {{ $i === $data['current_page'] ? 'active' : '' }}">
                                            <a class="page-link" href="{{ route('admin.reports.orders', array_merge(request()->query(), ['page' => $i])) }}"
                                                style="{{ $i === $data['current_page'] ? 'background-color: #28a745; border-color: #28a745;' : '' }}">
                                                {{ $i }}
                                            </a>
                                        </li>
                                    @endfor

                                    @if($data['current_page'] < $data['last_page'])
                                        <li class="page-item">
                                            <a class="page-link" href="{{ route('admin.reports.orders', array_merge(request()->query(), ['page' => $data['current_page'] + 1])) }}">
                                                {{ __('Next') }}
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="{{ route('admin.reports.orders', array_merge(request()->query(), ['page' => $data['last_page']])) }}">
                                                {{ __('Last') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-row:hover {
            background-color: #f8f9fa;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
    </style>
@endsection
