@extends('layout.app')

@section('title', __('areasettings::city.view_city'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::city.cities_management'), 'url' => route('admin.area-settings.cities.index')],
                    ['title' => __('areasettings::city.view_city')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h3 class="mb-0 fw-500">{{ __('areasettings::city.city_details') }}</h3>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.area-settings.cities.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('areasettings::city.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.area-settings.cities.edit', $city->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('areasettings::city.edit_city') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card  card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ __('common.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <x-translation-display :label="__('areasettings::city.name')" :model="$city" fieldName="name" :languages="$languages" />
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::city.country') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($city->country)
                                                            <span class="badge badge-primary badge-round badge-lg">
                                                                {{ $city->country->getTranslation('name', app()->getLocale()) ?? $city->country->code }}
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::city.activation') }}</label>
                                                    <p class="fs-15">
                                                        @if($city->active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ __('areasettings::city.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ __('areasettings::city.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::city.default') }}</label>
                                                    <p class="fs-15">
                                                        @if($city->default)
                                                            <span class="badge badge-success badge-round badge-lg">
                                                                <i class="uil uil-star me-1"></i>{{ __('areasettings::city.default') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-primary badge-round badge-lg">
                                                                <i class="uil uil-minus me-1"></i>{{ __('common.no') ?? 'No' }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card  card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ __('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $city->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $city->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .view-item label {
        color: #9299b8;
        margin-bottom: 8px;
    }
    .view-item p {
        margin-bottom: 0;
        font-weight: 500;
    }
</style>
@endpush
