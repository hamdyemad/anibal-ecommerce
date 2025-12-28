@extends('layout.app')
@section('title')
    {{ __('catalogmanagement::tax.view_tax') }} | Bnaia
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('catalogmanagement::tax.taxes_management'), 'url' => route('admin.taxes.index')],
                    ['title' => __('catalogmanagement::tax.view_tax')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('catalogmanagement::tax.tax_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.taxes.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('catalogmanagement::tax.back_to_list') }}
                            </a>
                            @can('taxes.edit')
                                <a href="{{ route('admin.taxes.edit', $tax->id) }}" class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-2"></i>{{ __('catalogmanagement::tax.edit_tax') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3><i class="uil uil-info-circle me-1"></i>{{ __('common.basic_information') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <x-translation-display :label="__('catalogmanagement::tax.name')" :model="$tax" fieldName="name" :languages="$languages" />
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::tax.percentage') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg">{{ $tax->percentage }}%</span>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::tax.status') }}</label>
                                                    <p class="fs-15">
                                                        @if($tax->is_active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ __('catalogmanagement::tax.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ __('catalogmanagement::tax.inactive') }}</span>
                                                        @endif
                                                    </p>
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
