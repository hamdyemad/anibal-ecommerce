@extends('layout.app')
@section('title', trans('catalogmanagement::bundle_category.view_bundle_category'))
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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::bundle_category.bundle_category_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.bundle-categories.edit', $bundleCategory->id) }}"
                               class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-edit"></i> {{ trans('main.edit') }}
                            </a>
                            <a href="{{ route('admin.bundle-categories.index') }}"
                               class="btn btn-light btn-default btn-squared text-capitalize">
                                <i class="uil uil-angle-left"></i> {{ trans('main.back') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <div class="row">
                                    <!-- Bundle Category Names -->
                                    @foreach($languages as $language)
                                        <div class="col-md-6 mb-25">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                    @if($language->code == 'ar')
                                                        الاسم ({{ $language->name }})
                                                    @else
                                                        {{ trans('catalogmanagement::bundle_category.name') }} ({{ $language->name }})
                                                    @endif
                                                </label>
                                                <div class="form-control ih-medium ip-gray radius-xs b-light px-15 bg-light" @if($language->rtl) dir="rtl" @endif>
                                                    {{ $bundleCategory->getTranslation('name', $language->code) ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Status -->
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">{{ trans('main.status') }}</label>
                                            <div class="form-control ih-medium ip-gray radius-xs b-light px-15 bg-light">
                                                @if($bundleCategory->active)
                                                    <span class="badge badge-success">{{ trans('main.active') }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ trans('main.inactive') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Created Date -->
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">{{ trans('main.created_at') }}</label>
                                            <div class="form-control ih-medium ip-gray radius-xs b-light px-15 bg-light">
                                                {{ $bundleCategory->created_at->format('Y-m-d H:i:s') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Updated Date -->
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">{{ trans('main.updated_at') }}</label>
                                            <div class="form-control ih-medium ip-gray radius-xs b-light px-15 bg-light">
                                                {{ $bundleCategory->updated_at->format('Y-m-d H:i:s') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bundle Category Image -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-10 d-block">{{ trans('catalogmanagement::bundle_category.image') }}</label>
                                    <div class="text-center">
                                        @if($bundleCategory->image)
                                            <img src="{{ asset('storage/' . $bundleCategory->image) }}"
                                                 alt="{{ $bundleCategory->getTranslation('name', app()->getLocale()) }}"
                                                 class="img-fluid rounded border"
                                                 style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                        @else
                                            <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                                                 style="width: 200px; height: 200px; margin: 0 auto;">
                                                <i class="uil uil-image fs-48 text-muted"></i>
                                            </div>
                                            <p class="text-muted mt-2">{{ trans('catalogmanagement::bundle_category.no_image') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Information Section -->
                        <div class="row mt-30">
                            <div class="col-12">
                                <h6 class="mb-20 fw-500 border-bottom pb-10">{{ trans('catalogmanagement::bundle_category.seo_information') }}</h6>
                            </div>

                            <!-- SEO Titles -->
                            @foreach($languages as $language)
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            @if($language->code == 'ar')
                                                عنوان SEO ({{ $language->name }})
                                            @else
                                                {{ trans('catalogmanagement::bundle_category.seo_title') }} ({{ $language->name }})
                                            @endif
                                        </label>
                                        <div class="form-control ih-medium ip-gray radius-xs b-light px-15 bg-light" @if($language->rtl) dir="rtl" @endif>
                                            {{ $bundleCategory->getTranslation('seo_title', $language->code) ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- SEO Descriptions -->
                            @foreach($languages as $language)
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            @if($language->code == 'ar')
                                                وصف SEO ({{ $language->name }})
                                            @else
                                                {{ trans('catalogmanagement::bundle_category.seo_description') }} ({{ $language->name }})
                                            @endif
                                        </label>
                                        <div class="form-control ip-gray radius-xs b-light px-15 bg-light"
                                             style="min-height: 80px; white-space: pre-wrap;" @if($language->rtl) dir="rtl" @endif>
                                            {{ $bundleCategory->getTranslation('seo_description', $language->code) ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- SEO Keywords -->
                            @foreach($languages as $language)
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            @if($language->code == 'ar')
                                                كلمات مفتاحية SEO ({{ $language->name }})
                                            @else
                                                {{ trans('catalogmanagement::bundle_category.seo_keywords') }} ({{ $language->name }})
                                            @endif
                                        </label>
                                        <div class="form-control ih-medium ip-gray radius-xs b-light px-15 bg-light" @if($language->rtl) dir="rtl" @endif>
                                            {{ $bundleCategory->getTranslation('seo_keywords', $language->code) ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Additional Information -->
                        <div class="row mt-30">
                            <div class="col-12">
                                <h6 class="mb-20 fw-500 border-bottom pb-10">{{ trans('catalogmanagement::bundle_category.additional_information') }}</h6>
                            </div>

                            <div class="col-md-6 mb-25">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-10 d-block">{{ trans('main.slug') }}</label>
                                    <div class="form-control ih-medium ip-gray radius-xs b-light px-15 bg-light">
                                        {{ $bundleCategory->slug }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-25">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-10 d-block">{{ trans('main.id') }}</label>
                                    <div class="form-control ih-medium ip-gray radius-xs b-light px-15 bg-light">
                                        {{ $bundleCategory->id }}
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
