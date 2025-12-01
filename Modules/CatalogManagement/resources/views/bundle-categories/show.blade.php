@extends('layout.app')

@section('title', trans('catalogmanagement::bundle_category.view_bundle_category'))

@push('styles')
<style>
/* Bundle Category View Styling */
.view-item {
    margin-bottom: 20px;
}

.view-item label {
    font-weight: 600;
    color: #5a5f7d;
    margin-bottom: 8px;
}

.view-item p {
    color: #272b41;
    font-size: 15px;
}

.card-holder {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    margin-bottom: 20px;
}

.card-holder .card-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
}

.card-holder .card-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.card-holder .card-body {
    padding: 25px;
}

.box-items-translations {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 15px;
}

.box-items-translations label {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 12px;
}
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::bundle_category.bundle_categories_management'), 'url' => route('admin.bundle-categories.index')],
                    ['title' => trans('catalogmanagement::bundle_category.view_bundle_category')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::bundle_category.bundle_category_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.bundle-categories.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.bundle-categories.edit', $bundleCategory->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                {{-- Basic Information --}}
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('catalogmanagement::bundle_category.basic_information') ?? 'Basic Information' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Bundle Category Names --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle_category.name') }}</label>
                                                    <div class="row">
                                                        @foreach($languages as $lang)
                                                            @php
                                                                $translation = $bundleCategory->getTranslation('name', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('main.status') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($bundleCategory->active)
                                                            <span class="badge badge-round badge-success badge-lg">{{ trans('main.active') }}</span>
                                                        @else
                                                            <span class="badge badge-round badge-secondary badge-lg">{{ trans('main.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Slug --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('main.slug') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <code>{{ $bundleCategory->slug ?? '-' }}</code>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created Date --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('main.created_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $bundleCategory->created_at->format('Y-m-d H:i:s') }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Updated Date --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('main.updated_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $bundleCategory->updated_at->format('Y-m-d H:i:s') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            {{-- Bundle Category Image --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ trans('catalogmanagement::bundle_category.image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($bundleCategory->image)
                                            <img src="{{ asset('storage/' . $bundleCategory->image) }}"
                                                 alt="{{ $bundleCategory->getTranslation('name', app()->getLocale()) }}"
                                                 class="img-fluid rounded border shadow-sm"
                                                 style="max-width: 100%; max-height: 300px; object-fit: cover;">
                                        @else
                                            <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                                                 style="width: 100%; height: 250px;">
                                                <i class="uil uil-image fs-48 text-muted"></i>
                                            </div>
                                            <p class="text-muted mt-2">{{ trans('catalogmanagement::bundle_category.no_image') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                                {{-- SEO Information --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-search me-1"></i>{{ trans('catalogmanagement::bundle_category.seo_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- SEO Titles --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle_category.seo_title') }}</label>
                                                    <div class="row">
                                                        @foreach($languages as $lang)
                                                            @php
                                                                $translation = $bundleCategory->getTranslation('seo_title', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- SEO Descriptions --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle_category.seo_description') }}</label>
                                                    <div class="row">
                                                        @foreach($languages as $lang)
                                                            @php
                                                                $translation = $bundleCategory->getTranslation('seo_description', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- SEO Keywords --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle_category.seo_keywords') }}</label>
                                                    <div class="row">
                                                        @foreach($languages as $lang)
                                                            @php
                                                                $translation = $bundleCategory->getTranslation('seo_keywords', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        @if(str_contains($translation, ','))
                                                                            @foreach(explode(',', $translation) as $keyword)
                                                                                <span class="badge badge-primary badge-lg badge-round me-1 mb-1"
                                                                                    style="@if($lang->code == 'ar') font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">{{ trim($keyword) }}</span>
                                                                            @endforeach
                                                                        @else
                                                                            <span class="badge badge-primary badge-lg badge-round"
                                                                                style="@if($lang->code == 'ar') font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">{{ $translation }}</span>
                                                                        @endif
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
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
