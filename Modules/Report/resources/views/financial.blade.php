@extends('layout.app')

@section('title')
    {{ trans('report::report.financial_reports') }}
@endsection

@section('content')
    <div class="crm mb-25">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <div class="breadcrumb-action justify-content-center flex-wrap">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="uil uil-estate"></i>{{ trans('dashboard.title') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">{{ trans('report::report.reports') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('report::report.financial_reports') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Vendors General Orders Data --}}
                @include('pages.dashboard.withdraw-transactions')
                
                {{-- Sales Overview --}}
                @include('pages.dashboard.stats-overview')
                
                {{-- Income & Expenses Charts --}}
                @include('pages.dashboard.income-and-expenses')
                
                {{-- Refunds Overview --}}
                @include('pages.dashboard.refunds-overview')
                
                {{-- Sales Charts --}}
                <div class="col-12">
                    <div class="row">
                        @include('pages.dashboard.total-sales-chart')
                        @include('pages.dashboard.earnings-chart')
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="row">
                        @include('pages.dashboard.refunds-chart')
                        @include('pages.dashboard.net-sales-chart')
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('pages.dashboard.charts-scripts')
@endsection
