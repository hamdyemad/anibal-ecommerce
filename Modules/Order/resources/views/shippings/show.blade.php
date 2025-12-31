@extends('layout.app')

@section('title', trans('shipping.shipping_details'))

@push('styles')
    <style>
        /* Custom styling for shipping show view */
        .card-holder {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
        }

        .card-header h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
        }

        .view-item {
            margin-bottom: 1rem;
        }

        .view-item label {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 0.5rem;
        }

        .box-items-translations {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
            border: 1px solid #e3e6f0;
        }

        .badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }

        .badge-success {
            background-color: #1cc88a;
        }

        .badge-secondary {
            background-color: #858796;
        }

        /* Cost display styling */
        .cost-display-wrapper {
            position: relative;
            transition: all 0.3s ease;
        }

        .cost-display-wrapper:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .badge-city {
            background-color: #0056B7;
            color: white;
            margin: 2px;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
        }

        .badge-category {
            background-color: #9C27B0;
            color: white;
            margin: 2px;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
        }

        .badge-department {
            background-color: #FF9800;
            color: white;
            margin: 2px;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
        }

        .badge-subcategory {
            background-color: #4CAF50;
            color: white;
            margin: 2px;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mb-4">
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
                    ['title' => trans('shipping.shipping_details')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('shipping.shipping_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.shippings.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('common.back_to_list') }}
                            </a>
                            @can('shippings.edit')
                                <a href="{{ route('admin.shippings.edit', $shipping->id) }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-2"></i>{{ __('common.edit') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information --}}
                            <div class="col-md-8 order-2 order-md-1">
                                <div class="card card-holder mb-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('shipping.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Name Translations --}}
                                            <x-translation-display :label="trans('shipping.name')" :model="$shipping" fieldName="title"
                                                :languages="\App\Models\Language::all()" />

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('shipping.status') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($shipping->active)
                                                            <span class="badge badge-success badge-lg badge-round">{{ trans('shipping.active') }}</span>
                                                        @else
                                                            <span class="badge badge-secondary badge-lg badge-round">{{ trans('shipping.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Country --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('shipping.country') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $shipping->country->name ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created Date --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('main.created_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $shipping->created_at }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Updated Date --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('main.updated_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $shipping->updated_at }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Cities & Categories/Departments/SubCategories --}}
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-map-marker me-1"></i>{{ trans('shipping.coverage') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Cities --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('shipping.cities') }}</label>
                                                    <div class="fs-15 color-dark fw-500">
                                                        @if($shipping->cities && $shipping->cities->count() > 0)
                                                            @foreach($shipping->cities as $city)
                                                                <span class="badge-city">{{ $city->name }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Departments/Categories/SubCategories based on settings --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    @if($shippingSettings?->shipping_allow_departments)
                                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('shipping.departments') }}</label>
                                                        <div class="fs-15 color-dark fw-500">
                                                            @if($shipping->departments && $shipping->departments->count() > 0)
                                                                @foreach($shipping->departments as $department)
                                                                    <span class="badge-city">{{ $department->name }}</span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </div>
                                                    @elseif($shippingSettings?->shipping_allow_categories)
                                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('shipping.categories') }}</label>
                                                        <div class="fs-15 color-dark fw-500">
                                                            @if($shipping->categories && $shipping->categories->count() > 0)
                                                                @foreach($shipping->categories as $category)
                                                                    <span class="badge-city">{{ $category->name }}</span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </div>
                                                    @elseif($shippingSettings?->shipping_allow_sub_categories)
                                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('shipping.sub_categories') }}</label>
                                                        <div class="fs-15 color-dark fw-500">
                                                            @if($shipping->subCategories && $shipping->subCategories->count() > 0)
                                                                @foreach($shipping->subCategories as $subCategory)
                                                                    <span class="badge-city">{{ $subCategory->name }}</span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Shipping Cost --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-money-bill me-1"></i>{{ trans('shipping.cost') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="cost-display-wrapper">
                                            <div class="d-flex flex-column align-items-center justify-content-center"
                                                style="height: 200px;">
                                                <div class="mb-3 d-flex align-items-center justify-content-center"
                                                    style="width: 100px; height: 100px; background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); border-radius: 50%; border: 3px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                                    <i class="uil uil-truck" style="font-size: 40px; color: white;"></i>
                                                </div>
                                                <h3 class="mb-2 fw-600 text-success">{{ currency() }} {{ number_format($shipping->cost, 2) }}</h3>
                                                <small class="text-muted">{{ trans('shipping.shipping_cost') }}</small>
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
