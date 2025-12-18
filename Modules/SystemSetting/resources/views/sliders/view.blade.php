@extends('layout.app')

@section('title', __('systemsetting::sliders.view_slider'))

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
                        'title' => __('systemsetting::sliders.sliders_management'),
                        'url' => route('admin.system-settings.sliders.index'),
                    ],
                    ['title' => __('systemsetting::sliders.view_slider')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::sliders.slider_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.system-settings.sliders.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('systemsetting::sliders.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.system-settings.sliders.edit', $slider->id) }}"
                                class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('systemsetting::sliders.edit') }}
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
                                            <i
                                                class="uil uil-info-circle me-1"></i>{{ __('systemsetting::sliders.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Slider Image --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::sliders.slider_image') }}</label>
                                                    <div class="fs-15">
                                                        @if ($slider->image)
                                                            @php
                                                                $width = '-';
                                                                $height = '-';
                                                                try {
                                                                    $attachment = $slider
                                                                        ->attachments()
                                                                        ->where('type', 'image')
                                                                        ->first();
                                                                    if ($attachment) {
                                                                        $filePath = storage_path(
                                                                            'app/public/' . $attachment->path,
                                                                        );
                                                                        if (file_exists($filePath)) {
                                                                            [$w, $h] = getimagesize($filePath);
                                                                            $width = $w . 'px';
                                                                            $height = $h . 'px';
                                                                        }
                                                                    }
                                                                } catch (\Exception $e) {
                                                                }
                                                            @endphp
                                                            <img src="{{ $slider->image }}" alt="Slider"
                                                                style="width: 100%; max-height: 400px; object-fit: contain; border-radius: 8px; border: 1px solid #eee;">
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Slider Link --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::sliders.slider_link') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($slider->slider_link)
                                                            <a href="{{ $slider->slider_link }}" target="_blank"
                                                                class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                                <i
                                                                    class="uil uil-external-link-alt me-1"></i>{{ __('systemsetting::sliders.visit_link') }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Sort Order --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::sliders.sort_order') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $slider->sort_order ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created At --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::sliders.created_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i
                                                            class="uil uil-calendar-alt me-1"></i>{{ $slider->created_at ? $slider->created_at : '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Updated At --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::sliders.updated_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i
                                                            class="uil uil-calendar-alt me-1"></i>{{ $slider->updated_at ? $slider->updated_at : '-' }}
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
