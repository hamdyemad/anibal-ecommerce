@extends('layout.app')

@section('title', __('systemsetting::about-us.about_us_mobile'))

@section('content')
<div class="container-fluid mb-3">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('systemsetting::about-us.about_us_mobile')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-default card-md mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ __('systemsetting::about-us.about_us_mobile') }}</h6>
                    <div class="btn-group">
                        <a href="{{ route('admin.system-settings.about-us.website') }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="uil uil-desktop me-1"></i> {{ __('systemsetting::about-us.website') }}
                        </a>
                        <a href="{{ route('admin.system-settings.about-us.mobile') }}" 
                           class="btn btn-sm btn-primary">
                            <i class="uil uil-mobile-android me-1"></i> {{ __('systemsetting::about-us.mobile') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="alertContainer"></div>
                    
                    <form id="aboutUsForm" action="{{ route('admin.system-settings.about-us.update', 'mobile') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Text (Multilingual) --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3"><i class="uil uil-text me-1"></i> {{ __('systemsetting::about-us.text') }}</h6>
                            </div>
                            <div class="col-12">
                                <x-multilingual-input 
                                    name="section_1_text" 
                                    label="Text"
                                    labelAr="النص"
                                    placeholder="Enter text"
                                    placeholderAr="أدخل النص"
                                    :languages="$languages" 
                                    :model="$aboutUs"
                                    type="ckeditor"
                                />
                            </div>
                        </div>

                        {{-- Video Link --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3"><i class="uil uil-video me-1"></i> {{ __('systemsetting::about-us.video_link') }}</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="section_2_video_link" class="form-label">{{ __('systemsetting::about-us.video_link') }}</label>
                                    <input type="url" 
                                           class="form-control" 
                                           id="section_2_video_link" 
                                           name="section_2_video_link" 
                                           value="{{ $aboutUs->section_2_video_link ?? '' }}"
                                           placeholder="{{ __('systemsetting::about-us.enter_video_link') }}"
                                           dir="ltr">
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="uil uil-check me-1"></i> {{ __('systemsetting::about-us.save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submission with AJAX
        const form = document.getElementById('aboutUsForm');
        const submitBtn = document.getElementById('submitBtn');
        const alertContainer = document.getElementById('alertContainer');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Update CKEditor content to textareas
            if (typeof CKEDITOR !== 'undefined') {
                for (let instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="uil uil-spinner-alt fa-spin me-1"></i> {{ __("systemsetting::about-us.saving") }}...';

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alertContainer.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="uil uil-check-circle me-1"></i> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                } else {
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="uil uil-exclamation-triangle me-1"></i> ${data.message || 'Error saving data'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="uil uil-exclamation-triangle me-1"></i> An error occurred. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="uil uil-check me-1"></i> {{ __("systemsetting::about-us.save") }}';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    });
</script>
@endpush
