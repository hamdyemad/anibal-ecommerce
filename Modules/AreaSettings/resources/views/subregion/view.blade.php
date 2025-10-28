@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::subregion.subregions_management'), 'url' => route('admin.area-settings.subregions.index')],
                    ['title' => __('areasettings::subregion.view_subregion')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('areasettings::subregion.subregion_details') }}</h4>
                        <div class="card-extra">
                            <a href="{{ route('admin.area-settings.subregions.edit', $subregion->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit"></i> {{ __('areasettings::subregion.edit_subregion') }}
                            </a>
                            <a href="{{ route('admin.area-settings.subregions.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left"></i> {{ __('areasettings::subregion.back_to_list') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ __('areasettings::subregion.basic_information') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('areasettings::subregion.id') }}:</strong></td>
                                                <td>{{ $subregion->id }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::subregion.country') }}:</strong></td>
                                                <td>{{ $subregion->region && $subregion->region->city && $subregion->region->city->country ? $subregion->region->city->country->getTranslation('name', app()->getLocale()) ?? $subregion->region->city->country->code : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::subregion.city') }}:</strong></td>
                                                <td>{{ $subregion->region && $subregion->region->city ? $subregion->region->city->getTranslation('name', app()->getLocale()) ?? $subregion->region->city->id : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::subregion.region') }}:</strong></td>
                                                <td>{{ $subregion->region ? $subregion->region->getTranslation('name', app()->getLocale()) ?? $subregion->region->id : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::subregion.status') }}:</strong></td>
                                                <td>
                                                    @if($subregion->active)
                                                        <span class="badge badge-success">
                                                            <i class="uil uil-check"></i> {{ __('areasettings::subregion.active') }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            <i class="uil uil-times"></i> {{ __('areasettings::subregion.inactive') }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::subregion.created_at') }}:</strong></td>
                                                <td>{{ $subregion->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::subregion.updated_at') }}:</strong></td>
                                                <td>{{ $subregion->updated_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ __('areasettings::subregion.translations') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            @foreach($languages as $language)
                                                <tr>
                                                    <td><strong>{{ __('areasettings::subregion.name') }} ({{ $language->name }}):</strong></td>
                                                    <td {{ $language->rtl ? 'dir=rtl' : '' }}>
                                                        {{ $subregion->getTranslation('name', $language->code) ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hierarchy Navigation -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ __('areasettings::subregion.hierarchy_navigation') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb">
                                                @if($subregion->region && $subregion->region->city && $subregion->region->city->country)
                                                    <li class="breadcrumb-item">
                                                        <a href="{{ route('admin.area-settings.countries.show', $subregion->region->city->country->id) }}">
                                                            <i class="uil uil-globe"></i> {{ $subregion->region->city->country->getTranslation('name', app()->getLocale()) ?? $subregion->region->city->country->code }}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($subregion->region && $subregion->region->city)
                                                    <li class="breadcrumb-item">
                                                        <a href="{{ route('admin.area-settings.cities.show', $subregion->region->city->id) }}">
                                                            <i class="uil uil-building"></i> {{ $subregion->region->city->getTranslation('name', app()->getLocale()) ?? $subregion->region->city->id }}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($subregion->region)
                                                    <li class="breadcrumb-item">
                                                        <a href="{{ route('admin.area-settings.regions.show', $subregion->region->id) }}">
                                                            <i class="uil uil-map-marker"></i> {{ $subregion->region->getTranslation('name', app()->getLocale()) ?? $subregion->region->id }}
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="breadcrumb-item active" aria-current="page">
                                                    <i class="uil uil-location-point"></i> {{ $subregion->getTranslation('name', app()->getLocale()) ?? $subregion->id }}
                                                </li>
                                            </ol>
                                        </nav>
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
