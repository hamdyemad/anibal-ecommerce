@extends('layout.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title">{{ __('menu.dashboard.title') }}</h4>
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="mb-4">
                        <i class="uil uil-smile-beam" style="font-size: 80px; color: #fff;"></i>
                    </div>
                    
                    <h1 class="text-white mb-3" style="font-size: 2.5rem; font-weight: 700;">
                        {{ __('dashboard.welcome') }} {{ $user_name }}! 👋
                    </h1>
                    
                    <p class="text-white mb-4" style="font-size: 1.2rem; opacity: 0.9;">
                        {{ __('dashboard.welcome_message') }}
                    </p>
                    
                    <div class="mt-4">
                        <p class="text-white mb-2" style="font-size: 1rem; opacity: 0.8;">
                            <i class="uil uil-clock"></i> {{ now()->format('l, F j, Y - h:i A') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Info Cards (Optional - No Queries) -->
    <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                <div class="card-body text-center py-4">
                    <i class="uil uil-check-circle" style="font-size: 50px; color: #28a745;"></i>
                    <h5 class="mt-3 mb-2">{{ __('dashboard.system_status') }}</h5>
                    <p class="text-success mb-0">{{ __('dashboard.all_systems_operational') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                <div class="card-body text-center py-4">
                    <i class="uil uil-user" style="font-size: 50px; color: #007bff;"></i>
                    <h5 class="mt-3 mb-2">{{ __('dashboard.your_role') }}</h5>
                    <p class="text-muted mb-0">
                        @if(Auth::user()->roles->isNotEmpty())
                            {{ Auth::user()->roles->first()->translations()->where('lang_key', 'name')->value('lang_value') ?? Auth::user()->roles->first()->name }}
                        @else
                            {{ __('dashboard.no_role') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                <div class="card-body text-center py-4">
                    <i class="uil uil-calendar-alt" style="font-size: 50px; color: #ffc107;"></i>
                    <h5 class="mt-3 mb-2">{{ __('dashboard.today') }}</h5>
                    <p class="text-muted mb-0">{{ now()->translatedFormat('l, j F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="mb-0">
                        <i class="uil uil-bolt"></i> {{ __('dashboard.quick_actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.products.index', ['lang' => app()->getLocale(), 'countryCode' => session('country_code', 'eg')]) }}" 
                               class="btn btn-outline-primary btn-block py-3" style="border-radius: 8px;">
                                <i class="uil uil-box"></i> {{ __('menu.products.title') }}
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.orders.index', ['lang' => app()->getLocale(), 'countryCode' => session('country_code', 'eg')]) }}" 
                               class="btn btn-outline-success btn-block py-3" style="border-radius: 8px;">
                                <i class="uil uil-shopping-cart"></i> {{ __('menu.orders.title') }}
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.customers.index', ['lang' => app()->getLocale(), 'countryCode' => session('country_code', 'eg')]) }}" 
                               class="btn btn-outline-info btn-block py-3" style="border-radius: 8px;">
                                <i class="uil uil-users-alt"></i> {{ __('menu.customers.title') }}
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.vendors.index', ['lang' => app()->getLocale(), 'countryCode' => session('country_code', 'eg')]) }}" 
                               class="btn btn-outline-warning btn-block py-3" style="border-radius: 8px;">
                                <i class="uil uil-store"></i> {{ __('menu.vendors.title') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
    
    .btn-outline-primary:hover,
    .btn-outline-success:hover,
    .btn-outline-info:hover,
    .btn-outline-warning:hover {
        transform: scale(1.05);
        transition: all 0.3s ease;
    }
</style>
@endsection
