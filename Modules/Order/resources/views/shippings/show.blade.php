@extends('layout.app')
@section('title')
    {{ trans('shipping.view_shipping') }} | Bnaia
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
                    [
                        'title' => trans('shipping.shipping_management'),
                        'url' => route('admin.shippings.index'),
                    ],
                    ['title' => trans('shipping.view_shipping')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-40">
                        <div class="d-flex justify-content-between align-items-center mb-30">
                            <h4 class="fw-bold">{{ trans('shipping.view_shipping') }}</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.shippings.edit', ['lang' => app()->getLocale(), 'countryCode' => session('country_code'), 'id' => $shipping->id]) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="uil uil-edit"></i> {{ trans('shipping.edit') }}
                                </a>
                                <a href="{{ route('admin.shippings.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="uil uil-arrow-left"></i> {{ trans('shipping.back') }}
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">{{ trans('shipping.title_en') }}</label>
                                <p class="text-muted">{{ $shipping->getTranslation('title', 'en') }}</p>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">{{ trans('shipping.title_ar') }}</label>
                                <p class="text-muted">{{ $shipping->getTranslation('title', 'ar') }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">{{ trans('shipping.cost') }}</label>
                                <p class="text-muted">{{ currency() }} {{ number_format($shipping->cost, 2) }}</p>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">{{ trans('shipping.status') }}</label>
                                <p>
                                    <span class="badge {{ $shipping->active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $shipping->active ? trans('shipping.active') : trans('shipping.inactive') }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-bold">{{ trans('shipping.country') }}</label>
                                <p class="text-muted">{{ $shipping->country->name ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">{{ trans('shipping.cities') }}</label>
                                @if($shipping->cities && $shipping->cities->count() > 0)
                                    <ul class="list-unstyled">
                                        @foreach($shipping->cities as $city)
                                            <li class="mb-1">
                                                <span class="badge badge-info">{{ $city->name }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">-</p>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">{{ trans('shipping.categories') }}</label>
                                @if($shipping->categories && $shipping->categories->count() > 0)
                                    <ul class="list-unstyled">
                                        @foreach($shipping->categories as $category)
                                            <li class="mb-1">
                                                <span class="badge badge-primary">{{ $category->name }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">-</p>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">{{ trans('shipping.created_at') }}</label>
                                <p class="text-muted">{{ $shipping->created_at }}</p>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">{{ trans('shipping.updated_at') }}</label>
                                <p class="text-muted">{{ $shipping->updated_at }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
