@extends('layout.app')

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
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('areasettings::city.city_details') }}</h4>
                        <div class="card-extra">
                            <a href="{{ route('admin.area-settings.cities.edit', $city->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit"></i> {{ __('areasettings::city.edit_city') }}
                            </a>
                            <a href="{{ route('admin.area-settings.cities.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left"></i> {{ __('areasettings::city.back_to_list') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ __('areasettings::city.basic_information') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('areasettings::city.id') }}:</strong></td>
                                                <td>{{ $city->id }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::city.country') }}:</strong></td>
                                                <td>{{ $city->country ? $city->country->getTranslation('name', app()->getLocale()) ?? $city->country->code : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::city.status') }}:</strong></td>
                                                <td>
                                                    @if($city->active)
                                                        <span class="badge badge-success">
                                                            <i class="uil uil-check"></i> {{ __('areasettings::city.active') }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            <i class="uil uil-times"></i> {{ __('areasettings::city.inactive') }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::city.created_at') }}:</strong></td>
                                                <td>{{ $city->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::city.updated_at') }}:</strong></td>
                                                <td>{{ $city->updated_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ __('areasettings::city.translations') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            @foreach($languages as $language)
                                                <tr>
                                                    <td><strong>{{ __('areasettings::city.name') }} ({{ $language->name }}):</strong></td>
                                                    <td {{ $language->rtl ? 'dir=rtl' : '' }}>
                                                        {{ $city->getTranslation('name', $language->code) ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Data -->
                        @if($city->regions && $city->regions->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ __('areasettings::city.regions') }} ({{ $city->regions->count() }})</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('areasettings::region.id') }}</th>
                                                        <th>{{ __('areasettings::region.name') }}</th>
                                                        <th>{{ __('areasettings::region.status') }}</th>
                                                        <th>{{ __('areasettings::region.created_at') }}</th>
                                                        <th>{{ __('areasettings::region.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($city->regions as $region)
                                                        <tr>
                                                            <td>{{ $region->id }}</td>
                                                            <td>{{ $region->getTranslation('name', app()->getLocale()) ?? '-' }}</td>
                                                            <td>
                                                                @if($region->active)
                                                                    <span class="badge badge-success">{{ __('areasettings::region.active') }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ __('areasettings::region.inactive') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $region->created_at->format('Y-m-d') }}</td>
                                                            <td>
                                                                <a href="{{ route('admin.area-settings.regions.show', $region->id) }}" class="btn btn-sm btn-info">
                                                                    <i class="uil uil-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.area-settings.regions.edit', $region->id) }}" class="btn btn-sm btn-warning">
                                                                    <i class="uil uil-edit"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
