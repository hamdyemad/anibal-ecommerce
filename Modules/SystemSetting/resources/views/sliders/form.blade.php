@extends('layout.app')

@section('title', isset($slider) ? __('systemsetting::sliders.edit_slider') : __('systemsetting::sliders.create_slider'))

@section('content')
<div class="container-fluid mb-3">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('systemsetting::sliders.sliders_management'), 'url' => route('admin.system-settings.sliders.index')],
                ['title' => isset($slider) ? __('systemsetting::sliders.edit_slider') : __('systemsetting::sliders.create_slider')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-default card-md mb-4">
                <div class="card-header">
                    <h6>{{ isset($slider) ? __('systemsetting::sliders.edit_slider') : __('systemsetting::sliders.create_slider') }}</h6>
                </div>
                <div class="card-body">
                    <div id="alertContainer"></div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                            <strong>Validation Errors</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="sliderForm" method="POST" action="{{ isset($slider) ? route('admin.system-settings.sliders.update', $slider->id) : route('admin.system-settings.sliders.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($slider))
                            @method('PUT')
                        @endif

                        <div class="card card-holder">
                            <div class="card-header">
                                <h3 class="fw-bold m-0">
                                    <i class="uil uil-info-circle me-1"></i>{{ __('systemsetting::sliders.basic_information') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Slider Title --}}
                                    @foreach($languages as $language)
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="title_{{ $language->id }}" class="form-label">
                                                    {{ __('systemsetting::sliders.title') }} ({{ $language->name }})
                                                </label>
                                                <input type="text"
                                                       name="translations[{{ $language->id }}][title]"
                                                       id="title_{{ $language->id }}"
                                                       class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                       placeholder="{{ __('systemsetting::sliders.title_placeholder') }}"
                                                       value="{{ old('translations.' . $language->id . '.title', isset($slider) ? $slider->getTranslation('title', $language->code) : '') }}"
                                                       @if($language->rtl) dir="rtl" @endif>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Slider Description --}}
                                    @foreach($languages as $language)
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="description_{{ $language->id }}" class="form-label">
                                                    {{ __('systemsetting::sliders.description') }} ({{ $language->name }})
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][description]"
                                                          id="description_{{ $language->id }}"
                                                          class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                          placeholder="{{ __('systemsetting::sliders.description_placeholder') }}"
                                                          rows="3"
                                                          @if($language->rtl) dir="rtl" @endif>{{ old('translations.' . $language->id . '.description', isset($slider) ? $slider->getTranslation('description', $language->code) : '') }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Media Type Selection --}}
                                    <div class="col-md-12">
                                        <div class="form-group mb-25">
                                            <label for="media_type" class="form-label">
                                                {{ __('systemsetting::sliders.media_type') }}
                                            </label>
                                            <select name="media_type" id="media_type" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('media_type') is-invalid @enderror">
                                                <option value="image" {{ old('media_type', isset($slider) ? $slider->media_type : 'image') == 'image' ? 'selected' : '' }}>
                                                    {{ __('systemsetting::sliders.image') }}
                                                </option>
                                                <option value="video" {{ old('media_type', isset($slider) ? $slider->media_type : 'image') == 'video' ? 'selected' : '' }}>
                                                    {{ __('systemsetting::sliders.video') }}
                                                </option>
                                            </select>
                                            @error('media_type')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Slider Image --}}
                                    <div class="col-md-6" id="image-upload-section">
                                        <x-image-upload
                                            id="image"
                                            name="image"
                                            :label="__('systemsetting::sliders.slider_image')"
                                            :existingImage="isset($slider) && $slider->image ? $slider->attachments()->where('type', 'image')->first()?->path : null"
                                            :placeholder="__('systemsetting::sliders.slider_image')"
                                            :recommendedSize="__('systemsetting::sliders.recommended_size')"
                                            aspectRatio="wide"
                                        />
                                    </div>

                                    {{-- Slider Video --}}
                                    <div class="col-md-6" id="video-upload-section">
                                        <x-video-upload
                                            id="video"
                                            name="video"
                                            :label="__('systemsetting::sliders.slider_video')"
                                            :existingVideo="isset($slider) && $slider->media_type == 'video' && $slider->video ? $slider->attachments()->where('type', 'video')->first()?->path : null"
                                            :placeholder="__('systemsetting::sliders.slider_video')"
                                            :recommendedSize="__('systemsetting::sliders.max_size') . ': 50MB'"
                                        />
                                    </div>

                                    {{-- Slider Link --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="slider_link" class="form-label">
                                                {{ __('systemsetting::sliders.slider_link') }}
                                            </label>
                                            <input type="url" name="slider_link" id="slider_link" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('slider_link') is-invalid @enderror"
                                                   placeholder="{{ __('systemsetting::sliders.slider_link_placeholder') }}"
                                                   value="{{ old('slider_link', isset($slider) ? $slider->slider_link : '') }}">
                                            @error('slider_link')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Sort Order --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="sort_order" class="form-label">
                                                {{ __('systemsetting::sliders.sort_order') }}
                                            </label>
                                            <input type="number" name="sort_order" id="sort_order" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('sort_order') is-invalid @enderror"
                                                   placeholder="0"
                                                   value="{{ old('sort_order', isset($slider) ? $slider->sort_order : 0) }}">
                                            @error('sort_order')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="button-group d-flex pt-25 justify-content-end">
                            <a href="{{ route('admin.system-settings.sliders.index') }}" class="btn btn-light btn-default btn-squared fw-400 text-capitalize me-2">
                                {{ __('systemsetting::sliders.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize">
                                {{ __('systemsetting::sliders.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Function to toggle between image and video upload sections
    function toggleMediaSections() {
        const mediaType = $('#media_type').val();
        
        if (mediaType === 'image') {
            $('#image-upload-section').show();
            $('#video-upload-section').hide();
        } else if (mediaType === 'video') {
            $('#image-upload-section').hide();
            $('#video-upload-section').show();
        }
    }

    // Initialize on page load
    toggleMediaSections();

    // Toggle on change
    $('#media_type').on('change', function() {
        toggleMediaSections();
    });

    const sliderForm = document.getElementById('sliderForm');

    if (sliderForm) {
        sliderForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const formData = new FormData(this);

            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

            if (window.LoadingOverlay) {
                window.LoadingOverlay.show();
            }

            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            fetch(this.action, {
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
                    if (window.LoadingOverlay) {
                        window.LoadingOverlay.showSuccess(
                            data.message || '{{ __("systemsetting::sliders.created_successfully") }}',
                            'Redirecting...'
                        );
                    }

                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 1000);
                } else {
                    if (window.LoadingOverlay) {
                        window.LoadingOverlay.hide();
                    }

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;

                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'An error occurred');
                    } else {
                        alert(data.message || 'An error occurred');
                    }

                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            let input = document.querySelector(`[name="${key}"]`);

                            if (input) {
                                input.classList.add('is-invalid');
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = data.errors[key][0];
                                input.parentNode.appendChild(errorDiv);
                            } else {
                                // Handle nested translation names like translations.1.title
                                const parts = key.split('.');
                                if (parts.length === 3) {
                                    const nestedInput = document.querySelector(`[name="translations[${parts[1]}][${parts[2]}]"]`);
                                    if (nestedInput) {
                                        nestedInput.classList.add('is-invalid');
                                        const errorDiv = document.createElement('div');
                                        errorDiv.className = 'invalid-feedback';
                                        errorDiv.textContent = data.errors[key][0];
                                        nestedInput.parentNode.appendChild(errorDiv);
                                    }
                                }
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);

                if (window.LoadingOverlay) {
                    window.LoadingOverlay.hide();
                }

                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred');
                } else {
                    alert('An error occurred');
                }
            });
        });

        document.querySelectorAll('input, select, textarea').forEach(input => {
            const clearError = () => {
                input.classList.remove('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            };

            input.addEventListener('input', clearError);
            input.addEventListener('change', clearError);
        });
    }
});
</script>
@endpush
