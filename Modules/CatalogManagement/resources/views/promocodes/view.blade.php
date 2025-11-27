@extends('layout.app')

@section('title', __('catalogmanagement::promocodes.view_promocode'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('catalogmanagement::promocodes.title'), 'url' => route('admin.promocodes.index')],
                    ['title' => __('catalogmanagement::promocodes.view_promocode')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('catalogmanagement::promocodes.promocode_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.promocodes.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('catalogmanagement::promocodes.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.promocodes.edit', $promocode->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('catalogmanagement::promocodes.edit_promocode') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ __('catalogmanagement::promocodes.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::promocodes.code') }}</label>
                                                    <p class="fs-15 color-dark fw-bold">
                                                        {{ $promocode->code }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::promocodes.maximum_of_use') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $promocode->maximum_of_use }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::promocodes.type') }}</label>
                                                    <p class="fs-15 color-dark text-capitalize">
                                                        {{ __('catalogmanagement::promocodes.types.'.$promocode->type) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::promocodes.value') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $promocode->value }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::promocodes.dedicated_to') }}</label>
                                                    <p class="fs-15 color-dark text-capitalize">
                                                        {{ __('catalogmanagement::promocodes.dedicated_options.'.$promocode->dedicated_to) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::promocodes.status') }}</label>
                                                    <p class="fs-15">
                                                        @if($promocode->is_active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ __('catalogmanagement::promocodes.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ __('catalogmanagement::promocodes.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ __('catalogmanagement::promocodes.validity_timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::promocodes.valid_from') }}</label>
                                                    <p class="fs-15 color-dark">{{ $promocode->valid_from->format('Y-m-d') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::promocodes.valid_until') }}</label>
                                                    <p class="fs-15 color-dark">{{ $promocode->valid_until->format('Y-m-d') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $promocode->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $promocode->updated_at }}</p>
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
