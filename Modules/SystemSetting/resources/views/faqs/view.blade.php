@extends('layout.app')

@section('title', __('systemsetting::faqs.view_faq'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                [
                    'title' => __('common.dashboard'),
                    'url' => route('admin.dashboard'),
                    'icon' => 'uil uil-estate',
                ],
                [
                    'title' => __('systemsetting::faqs.faqs_management'),
                    'url' => route('admin.system-settings.faqs.index'),
                ],
                ['title' => __('systemsetting::faqs.view_faq')],
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-500">{{ __('systemsetting::faqs.faq_details') }}</h5>
                    <div class="d-flex gap-10">
                        <a href="{{ route('admin.system-settings.faqs.index') }}" class="btn btn-light btn-sm">
                            <i class="uil uil-arrow-left me-2"></i>{{ __('systemsetting::faqs.back_to_list') }}
                        </a>
                        <a href="{{ route('admin.system-settings.faqs.edit', $faq->id) }}" class="btn btn-primary btn-sm">
                            <i class="uil uil-edit me-2"></i>{{ __('systemsetting::faqs.edit') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            {{-- Basic Information --}}
                            <div class="card card-holder mb-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-info-circle me-1"></i>{{ __('systemsetting::faqs.basic_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Status --}}
                                        <div class="col-md-12 mb-3">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::faqs.status') }}</label>
                                                <p class="fs-15 color-dark fw-500">
                                                    @if($faq->active)
                                                        <span class="badge badge-round badge-success badge-lg">
                                                            <i class="uil uil-check-circle me-1"></i>{{ __('systemsetting::faqs.active') }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-round badge-danger badge-lg">
                                                            <i class="uil uil-times-circle me-1"></i>{{ __('systemsetting::faqs.inactive') }}
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Created At --}}
                                        <div class="col-md-6">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::faqs.created_at') }}</label>
                                                <p class="fs-15 color-dark fw-500">
                                                    <i class="uil uil-calendar-alt me-1"></i>{{ $faq->created_at ? $faq->created_at : '-' }}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Updated At --}}
                                        <div class="col-md-6">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::faqs.updated_at') }}</label>
                                                <p class="fs-15 color-dark fw-500">
                                                    <i class="uil uil-calendar-alt me-1"></i>{{ $faq->updated_at ? $faq->updated_at : '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Translations --}}
                            <div class="card card-holder">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-language me-1"></i>{{ __('common.translations') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    @foreach($languages as $language)
                                        @php
                                            $question = $faq->getTranslation('question', $language->code);
                                            $answer = $faq->getTranslation('answer', $language->code);
                                        @endphp
                                        <div class="translation-section mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                            <h6 class="mb-3 text-primary">
                                                <i class="uil uil-globe me-2"></i>{{ $language->name }}
                                            </h6>
                                            <div class="row">
                                                {{-- Question --}}
                                                <div class="col-md-12 mb-3">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::faqs.question') }}</label>
                                                        <p class="fs-15 color-dark fw-500" @if($language->code == 'ar') dir="rtl" @endif>
                                                            {{ $question ?: '-' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                {{-- Answer --}}
                                                <div class="col-md-12">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::faqs.answer') }}</label>
                                                        <div class="fs-15 color-dark" @if($language->code == 'ar') dir="rtl" @endif>
                                                            {!! $answer ?: '<span class="text-muted">-</span>' !!}
                                                        </div>
                                                    </div>
                                                </div>
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
@endsection
