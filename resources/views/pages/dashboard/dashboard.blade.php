@extends('layout.app')

@section('title')
    {{ $title ?? 'Dashboard' }}
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
                                    <li class="breadcrumb-item"><a href="#"><i
                                                class="uil uil-estate"></i>{{ trans('dashboard.title') }}</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Removed sections: withdraw-transactions and stats-overview --}}
                {{-- These can be moved to reports section if needed --}}

                {{-- Removed charts sections: income-and-expenses, refunds-overview, and all sales charts --}}
                {{-- These have been moved to reports module --}}

                @include('pages.dashboard.stats-cards')
                @include('pages.dashboard.orders-overview')



                @include('pages.dashboard.top-selling-products')
                @include('pages.dashboard.latest-orders')
                @include('pages.dashboard.best-customers')

                @if (isAdmin())
                    @include('pages.dashboard.top-vendors')
                @endif
                @include('pages.dashboard.recent-activities')

            </div>
            <!-- ends: .row -->
        </div>
    </div>

    @include('pages.dashboard.charts-scripts')
@endsection
