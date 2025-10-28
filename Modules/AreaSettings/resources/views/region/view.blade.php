@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::region.regions_management'), 'url' => route('admin.area-settings.regions.index')],
                    ['title' => __('areasettings::region.view_region')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('areasettings::region.region_details') }}</h4>
                        <div class="card-extra">
                            <a href="{{ route('admin.area-settings.regions.edit', $region->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit"></i> {{ __('areasettings::region.edit_region') }}
                            </a>
                            <a href="{{ route('admin.area-settings.regions.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left"></i> {{ __('areasettings::region.back_to_list') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ __('areasettings::region.basic_information') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('areasettings::region.id') }}:</strong></td>
                                                <td>{{ $region->id }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::region.country') }}:</strong></td>
                                                <td>{{ $region->city && $region->city->country ? $region->city->country->getTranslation('name', app()->getLocale()) ?? $region->city->country->code : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::region.city') }}:</strong></td>
                                                <td>{{ $region->city ? $region->city->getTranslation('name', app()->getLocale()) ?? $region->city->id : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::region.status') }}:</strong></td>
                                                <td>
                                                    @if($region->active)
                                                        <span class="badge badge-success">
                                                            <i class="uil uil-check"></i> {{ __('areasettings::region.active') }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            <i class="uil uil-times"></i> {{ __('areasettings::region.inactive') }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::region.created_at') }}:</strong></td>
                                                <td>{{ $region->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('areasettings::region.updated_at') }}:</strong></td>
                                                <td>{{ $region->updated_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ __('areasettings::region.translations') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            @foreach($languages as $language)
                                                <tr>
                                                    <td><strong>{{ __('areasettings::region.name') }} ({{ $language->name }}):</strong></td>
                                                    <td {{ $language->rtl ? 'dir=rtl' : '' }}>
                                                        {{ $region->getTranslation('name', $language->code) ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Data -->
                        @if($region->subRegions && $region->subRegions->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ __('areasettings::region.subregions') }} ({{ $region->subRegions->count() }})</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('areasettings::subregion.id') }}</th>
                                                        <th>{{ __('areasettings::subregion.name') }}</th>
                                                        <th>{{ __('areasettings::subregion.status') }}</th>
                                                        <th>{{ __('areasettings::subregion.created_at') }}</th>
                                                        <th>{{ __('areasettings::subregion.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($region->subRegions as $subRegion)
                                                        <tr>
                                                            <td>{{ $subRegion->id }}</td>
                                                            <td>{{ $subRegion->getTranslation('name', app()->getLocale()) ?? '-' }}</td>
                                                            <td>
                                                                @if($subRegion->active)
                                                                    <span class="badge badge-success">{{ __('areasettings::subregion.active') }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ __('areasettings::subregion.inactive') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $subRegion->created_at->format('Y-m-d') }}</td>
                                                            <td>
                                                                <a href="{{ route('admin.area-settings.subregions.show', $subRegion->id) }}" class="btn btn-sm btn-info">
                                                                    <i class="uil uil-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.area-settings.subregions.edit', $subRegion->id) }}" class="btn btn-sm btn-warning">
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
