@extends('layout.app')

@section('title')
    {{ trans('menu.reports.title') }}
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
                    ['title' => trans('menu.reports.title')],
                ]" />
            </div>
        </div>

        <!-- Reports Grid -->
        <div class="row">
            <!-- Financial Reports Card -->
            <div class="col-lg-4 col-md-6 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl h-100" style="border-left: 4px solid #6c757d;">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar mb-3">
                                <h2 class="ap-po-details__title" style="color: #6c757d; font-size: 1.5rem;">
                                    {{ trans('report::report.financial_reports') }}
                                </h2>
                                <p class="ap-po-details__text text-muted">{{ __('Income, expenses, and sales analytics') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 70px; height: 70px; background: linear-gradient(135deg, #6c757d, #495057); color: white;">
                                    <i class="uil uil-chart-line" style="font-size: 32px;"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.reports.financial') }}" class="btn btn-secondary btn-sm w-100">
                                    <i class="uil uil-arrow-right me-1"></i>{{ __('View Report') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registered Users Report Card -->
            <div class="col-lg-4 col-md-6 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl h-100" style="border-left: 4px solid {{ config('branding.colors.primary') }};">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar mb-3">
                                <h2 class="ap-po-details__title" style="color: {{ config('branding.colors.primary') }}; font-size: 1.5rem;">
                                    {{ trans('menu.reports.registerd users') }}
                                </h2>
                                <p class="ap-po-details__text text-muted">{{ __('Comprehensive user registration statistics') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 70px; height: 70px; background: linear-gradient(135deg, {{ config('branding.colors.primary') }}, #0d47a1); color: white;">
                                    <i class="uil uil-users-alt" style="font-size: 32px;"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.reports.registered-users') }}" class="btn btn-primary btn-sm w-100">
                                    <i class="uil uil-arrow-right me-1"></i>{{ __('View Report') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Area Users Report Card -->
            <div class="col-lg-4 col-md-6 mb-25">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl h-100" style="border-left: 4px solid {{ config('branding.colors.secondary') }};">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar mb-3">
                                <h2 class="ap-po-details__title" style="color: {{ config('branding.colors.secondary') }}; font-size: 1.5rem;">
                                    {{ trans('menu.reports.area users') }}
                                </h2>
                                <p class="ap-po-details__text text-muted">{{ __('Geographic distribution analysis') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 70px; height: 70px; background: linear-gradient(135deg, {{ config('branding.colors.secondary') }}, #d32f2f); color: white;">
                                    <i class="uil uil-map-marker" style="font-size: 32px;"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.reports.area-users') }}" class="btn btn-danger btn-sm w-100">
                                    <i class="uil uil-arrow-right me-1"></i>{{ __('View Report') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Report Card -->
            <div class="col-lg-4 col-md-6 mb-25">
                <div class="ap-po-details ap-po-details--4 p-25 radius-xl h-100" style="border-left: 4px solid #28a745;">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar mb-3">
                                <h2 class="ap-po-details__title" style="color: #28a745; font-size: 1.5rem;">
                                    {{ trans('menu.reports.orders report') }}
                                </h2>
                                <p class="ap-po-details__text text-muted">{{ __('Sales and order analytics') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 70px; height: 70px; background: linear-gradient(135deg, #28a745, #1e7e34); color: white;">
                                    <i class="uil uil-shopping-cart" style="font-size: 32px;"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.reports.orders') }}" class="btn btn-success btn-sm w-100">
                                    <i class="uil uil-arrow-right me-1"></i>{{ __('View Report') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Report Card -->
            <div class="col-lg-4 col-md-6 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl h-100" style="border-left: 4px solid #ffc107;">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar mb-3">
                                <h2 class="ap-po-details__title" style="color: #ffc107; font-size: 1.5rem;">
                                    {{ trans('menu.reports.product report') }}
                                </h2>
                                <p class="ap-po-details__text text-muted">{{ __('Product inventory & performance') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 70px; height: 70px; background: linear-gradient(135deg, #ffc107, #ff9800); color: white;">
                                    <i class="uil uil-box" style="font-size: 32px;"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.reports.products') }}" class="btn btn-warning btn-sm w-100">
                                    <i class="uil uil-arrow-right me-1"></i>{{ __('View Report') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Points Report Card -->
            <div class="col-lg-4 col-md-6 mb-25">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl h-100" style="border-left: 4px solid #17a2b8;">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar mb-3">
                                <h2 class="ap-po-details__title" style="color: #17a2b8; font-size: 1.5rem;">
                                    {{ trans('menu.reports.points report') }}
                                </h2>
                                <p class="ap-po-details__text text-muted">{{ __('Customer loyalty points system') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 70px; height: 70px; background: linear-gradient(135deg, #17a2b8, #0c5460); color: white;">
                                    <i class="uil uil-award" style="font-size: 32px;"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.reports.points') }}" class="btn btn-info btn-sm w-100">
                                    <i class="uil uil-arrow-right me-1"></i>{{ __('View Report') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
