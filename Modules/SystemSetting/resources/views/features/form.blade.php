@extends('layout.app')

@section('title', __('systemsetting::features.features_management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                [
                    'title' => __('menu.dashboard.title'),
                    'url' => route('admin.dashboard'),
                    'icon' => 'uil uil-estate',
                ],
                ['title' => __('systemsetting::features.features_management')],
            ]" />
        </div>
    </div>

    <form id="featuresForm" action="{{ route('admin.system-settings.features.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::features.features_management') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($features as $index => $feature)
                            <div class="card card-holder mb-3">
                                <div class="card-header">
                                    <h6 class="m-0 text-white">
                                        <i class="uil uil-star me-2"></i>{{ __('systemsetting::features.feature_details') }} {{ $index + 1 }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="feature-section">

                                        <input type="hidden" name="features[{{ $index }}][id]" value="{{ $feature->id ?? '' }}">

                                        <div class="row">
                                            {{-- Logo Upload --}}
                                            <div class="col-md-3">
                                                <x-image-upload
                                                    id="feature_logo_{{ $index }}"
                                                    name="features[{{ $index }}][logo]"
                                                    :label="__('systemsetting::features.logo')"
                                                    :existingImage="$feature->logo ?? null"
                                                    :placeholder="__('systemsetting::features.logo')"
                                                    :recommendedSize="__('systemsetting::features.logo_recommended_size')"
                                                    aspectRatio="square"
                                                />
                                            </div>

                                            {{-- Title and Subtitle --}}
                                            <div class="col-md-9">
                                                <div class="row">
                                                    {{-- Title (Multilingual) --}}
                                                    <div class="col-md-12 mb-3">
                                                        <x-multilingual-input
                                                            name="title"
                                                            oldPrefix="features[{{ $index }}][translations]"
                                                            label="Title"
                                                            :labelAr="'العنوان'"
                                                            :placeholder="'Title'"
                                                            :placeholderAr="'العنوان'"
                                                            type="text"
                                                            :languages="$languages"
                                                            :model="$feature ?? null"
                                                        />
                                                    </div>

                                                    {{-- Subtitle (Multilingual) --}}
                                                    <div class="col-md-12 mb-3">
                                                        <x-multilingual-input
                                                            name="subtitle"
                                                            oldPrefix="features[{{ $index }}][translations]"
                                                            label="Subtitle"
                                                            :labelAr="'العنوان الفرعى'"
                                                            :placeholder="'Subtitle'"
                                                            :placeholderAr="'العنوان الفرعى'"
                                                            type="text"
                                                            :languages="$languages"
                                                            :model="$feature ?? null"
                                                            :required="false"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Form Actions --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary btn-default btn-squared">
                                        <i class="uil uil-save me-2"></i>{{ __('systemsetting::features.save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const featuresForm = document.getElementById('featuresForm');

    if (featuresForm) {
        featuresForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const formData = new FormData(this);

            // Disable submit button and show loading
            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> {{ __("common.processing") ?? "Processing..." }}';

            // Show loading overlay
            if (window.LoadingOverlay) {
                window.LoadingOverlay.show();
            }

            // Clear previous errors
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
                    // Show success message
                    if (window.LoadingOverlay) {
                        window.LoadingOverlay.showSuccess(
                            data.message || '{{ __("systemsetting::features.saved_successfully") }}',
                            '{{ __("common.redirecting") ?? "Redirecting..." }}'
                        );
                    }

                    // Redirect after short delay
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 1000);
                } else {
                    // Hide loading overlay
                    if (window.LoadingOverlay) {
                        window.LoadingOverlay.hide();
                    }

                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;

                    // Show error message
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || '{{ __("common.error_occurred") }}');
                    } else {
                        alert(data.message || '{{ __("common.error_occurred") }}');
                    }

                    // Display validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            // Try to find input by exact name match
                            let input = document.querySelector(`[name="${key}"]`);

                            // If not found, convert dot notation to bracket notation
                            if (!input) {
                                const parts = key.split('.');
                                let bracketKey = parts[0];
                                for (let i = 1; i < parts.length; i++) {
                                    bracketKey += '[' + parts[i] + ']';
                                }
                                input = document.querySelector(`[name="${bracketKey}"]`);
                            }

                            if (input) {
                                input.classList.add('is-invalid');
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = data.errors[key][0];
                                input.parentNode.appendChild(errorDiv);
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);

                // Hide loading overlay
                if (window.LoadingOverlay) {
                    window.LoadingOverlay.hide();
                }

                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                // Show error message
                if (typeof toastr !== 'undefined') {
                    toastr.error('{{ __("common.error_occurred") }}');
                } else {
                    alert('{{ __("common.error_occurred") }}');
                }
            });
        });

        // Clear errors on input
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
