@extends('layout.app')

@push('styles')
<!-- Select2 CSS -->
<style>
    .wizard-step-content {
        display: none;
    }
    .wizard-step-content.active {
        display: block;
    }
    .form-control:focus {
        border-color: #5F63F2;
        box-shadow: 0 0 0 0.2rem rgba(95, 99, 242, 0.25);
    }
    .image-preview-container {
        position: relative;
        width: 150px;
        height: 150px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s;
    }
    .image-preview-container:hover {
        border-color: #5F63F2;
        background: #f0f1ff;
    }
    .image-preview {
        max-width: 100%;
        max-height: 100%;
        display: none;
    }
    .image-preview.show {
        display: block;
    }
    .upload-icon {
        font-size: 48px;
        color: #adb5bd;
    }
    .select2-selection--multiple .select2-selection__choice {
        background-color: #5F63F2 !important;
        border-color: #5F63F2 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => trans('vendor::vendor.vendors_management'), 'url' => route('admin.vendors.index')],
                ['title' => isset($vendor) ? trans('vendor::vendor.edit_vendor') : trans('vendor::vendor.create_vendor')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($vendor) ? trans('vendor::vendor.edit_vendor') : trans('vendor::vendor.create_vendor') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Wizard Navigation -->
                    <x-wizard :steps="[
                        trans('vendor::vendor.vendor_information'),
                        trans('vendor::vendor.vendor_documents'),
                        trans('vendor::vendor.vendor_account_details'),
                        trans('vendor::vendor.review_submit')
                    ]" :currentStep="1" />

                    <!-- Form -->
                    <form id="vendorForm" method="POST" action="{{ isset($vendor) ? route('admin.vendors.update', $vendor->id) : route('admin.vendors.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($vendor))
                            @method('PUT')
                        @endif

                        <!-- Step 1: Vendor Information -->
                        <div class="wizard-step-content active" data-step="1">
                            <h5 class="mb-4">{{ trans('vendor::vendor.vendor_information') }}</h5>

                            <div class="row">
                                <!-- Name Fields for each language -->
                                @foreach($languages as $language)
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="name_{{ $language->code }}" class="form-label {{ $language->rtl ? 'text-end' : '' }}">
                                            {{ trans('vendor::vendor.name') }} ({{ $language->name }}) 
                                            @if($language->code == 'en')
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input 
                                            type="text" 
                                            name="translations[{{ $language->id }}][name]"
                                            id="name_{{ $language->code }}"
                                            class="form-control"
                                            placeholder="{{ trans('vendor::vendor.enter_vendor_name_in') }} {{ $language->name }}"
                                            value="{{ isset($vendor) ? $vendor->getTranslation('name', $language->code) : old('translations.'.$language->id.'.name') }}"
                                            {{ $language->rtl ? 'dir=rtl' : '' }}
                                            {{ $language->code == 'en' ? 'required' : '' }}
                                        >
                                        @error('translations.'.$language->id.'.name')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="row">
                                <!-- Description Fields for each language -->
                                @foreach($languages as $language)
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="description_{{ $language->code }}" class="form-label {{ $language->rtl ? 'text-end' : '' }}">
                                            {{ trans('vendor::vendor.description') }} ({{ $language->name }})
                                        </label>
                                        <textarea 
                                            name="translations[{{ $language->id }}][description]"
                                            id="description_{{ $language->code }}"
                                            class="form-control"
                                            rows="4"
                                            placeholder="{{ trans('vendor::vendor.enter_vendor_description_in') }} {{ $language->name }}"
                                            {{ $language->rtl ? 'dir=rtl' : '' }}
                                        >{{ isset($vendor) ? $vendor->getTranslation('description', $language->code) : old('translations.'.$language->id.'.description') }}</textarea>
                                        @error('translations.'.$language->id.'.description')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="row">
                                <!-- Activities Selection -->
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="activities" class="form-label">
                                            {{ trans('vendor::vendor.activities') }} 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="activities[]" id="activities" class="form-control select2" multiple required>
                                            @foreach($activities as $activity)
                                                <option value="{{ $activity->id }}" 
                                                    {{ isset($vendor) && $vendor->activities->contains($activity->id) ? 'selected' : '' }}>
                                                    {{ $activity->getTranslation('name', app()->getLocale()) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('activities')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Information -->
                            <h5 class="mb-3 mt-4">{{ trans('vendor::vendor.seo_information') }}</h5>
                            
                            <div class="row">
                                <!-- Meta Title for each language -->
                                @foreach($languages as $language)
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="meta_title_{{ $language->code }}" class="form-label {{ $language->rtl ? 'text-end' : '' }}">
                                            {{ trans('vendor::vendor.meta_title') }} ({{ $language->name }})
                                        </label>
                                        <input 
                                            type="text" 
                                            name="translations[{{ $language->id }}][meta_title]"
                                            id="meta_title_{{ $language->code }}"
                                            class="form-control"
                                            placeholder="{{ trans('vendor::vendor.enter_meta_title') }}"
                                            value="{{ isset($vendor) ? $vendor->getTranslation('meta_title', $language->code) : old('translations.'.$language->id.'.meta_title') }}"
                                            {{ $language->rtl ? 'dir=rtl' : '' }}
                                        >
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="row">
                                <!-- Meta Description for each language -->
                                @foreach($languages as $language)
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="meta_description_{{ $language->code }}" class="form-label {{ $language->rtl ? 'text-end' : '' }}">
                                            {{ trans('vendor::vendor.meta_description') }} ({{ $language->name }})
                                        </label>
                                        <textarea 
                                            name="translations[{{ $language->id }}][meta_description]"
                                            id="meta_description_{{ $language->code }}"
                                            class="form-control"
                                            rows="3"
                                            placeholder="{{ trans('vendor::vendor.enter_meta_description') }}"
                                            {{ $language->rtl ? 'dir=rtl' : '' }}
                                        >{{ isset($vendor) ? $vendor->getTranslation('meta_description', $language->code) : old('translations.'.$language->id.'.meta_description') }}</textarea>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="row">
                                <!-- Meta Keywords for each language -->
                                @foreach($languages as $language)
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="meta_keywords_{{ $language->code }}" class="form-label {{ $language->rtl ? 'text-end' : '' }}">
                                            {{ trans('vendor::vendor.meta_keywords') }} ({{ $language->name }})
                                        </label>
                                        <input 
                                            type="text" 
                                            name="translations[{{ $language->id }}][meta_keywords]"
                                            id="meta_keywords_{{ $language->code }}"
                                            class="form-control"
                                            placeholder="{{ trans('vendor::vendor.separate_keywords_commas') }}"
                                            value="{{ isset($vendor) ? $vendor->getTranslation('meta_keywords', $language->code) : old('translations.'.$language->id.'.meta_keywords') }}"
                                            {{ $language->rtl ? 'dir=rtl' : '' }}
                                        >
                                        <small class="text-muted">{{ trans('vendor::vendor.separate_keywords_commas') }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 2: Vendor Documents (Placeholder) -->
                        <div class="wizard-step-content" data-step="2">
                            <h5 class="mb-4">{{ trans('vendor::vendor.vendor_documents') }}</h5>
                            <p class="text-muted">Document upload fields will be added here...</p>
                        </div>

                        <!-- Step 3: Vendor Account Details (Placeholder) -->
                        <div class="wizard-step-content" data-step="3">
                            <h5 class="mb-4">{{ trans('vendor::vendor.vendor_account_details') }}</h5>
                            <p class="text-muted">Account details fields will be added here...</p>
                        </div>

                        <!-- Step 4: Review & Submit (Placeholder) -->
                        <div class="wizard-step-content" data-step="4">
                            <h5 class="mb-4">{{ trans('vendor::vendor.review_submit') }}</h5>
                            <p class="text-muted">{{ trans('vendor::vendor.please_review_info') }}</p>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="prevBtn" class="btn btn-light btn-squared" style="display: none;">
                                <i class="uil uil-arrow-left"></i> {{ trans('vendor::vendor.previous') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <a href="{{ route('admin.vendors.index') }}" class="btn btn-light btn-squared">
                                    <i class="uil uil-times"></i> {{ trans('vendor::vendor.cancel') }}
                                </a>
                                <button type="button" id="nextBtn" class="btn btn-primary btn-squared">
                                    {{ trans('vendor::vendor.next') }} <i class="uil uil-arrow-right"></i>
                                </button>
                                <button type="submit" id="submitBtn" class="btn btn-success btn-squared" style="display: none;">
                                    <i class="uil uil-check"></i> {{ isset($vendor) ? trans('vendor::vendor.update_vendor') : trans('vendor::vendor.create_vendor_button') }}
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

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 4;

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '{{ trans("vendor::vendor.please_select_activity") }}'
    });

    // Show/Hide steps
    function showStep(step) {
        $('.wizard-step-content').removeClass('active');
        $(`.wizard-step-content[data-step="${step}"]`).addClass('active');
        
        $('.wizard-step-nav').removeClass('current');
        $(`.wizard-step-nav[data-step="${step}"]`).addClass('current');
        
        // Update buttons
        if (step === 1) {
            $('#prevBtn').hide();
        } else {
            $('#prevBtn').show();
        }
        
        if (step === totalSteps) {
            $('#nextBtn').hide();
            $('#submitBtn').show();
        } else {
            $('#nextBtn').show();
            $('#submitBtn').hide();
        }
    }

    // Next button
    $('#nextBtn').on('click', function() {
        if (validateCurrentStep()) {
            currentStep++;
            if (currentStep > totalSteps) currentStep = totalSteps;
            showStep(currentStep);
        }
    });

    // Previous button
    $('#prevBtn').on('click', function() {
        currentStep--;
        if (currentStep < 1) currentStep = 1;
        showStep(currentStep);
    });

    // Click on wizard step navigation
    $('.wizard-step-nav').on('click', function() {
        const step = parseInt($(this).data('step'));
        if (step < currentStep || validateCurrentStep()) {
            currentStep = step;
            showStep(currentStep);
        }
    });

    // Validate current step
    function validateCurrentStep() {
        if (currentStep === 1) {
            // Validate Step 1
            let isValid = true;
            
            // Check required name field (English)
            const nameEn = $('input[name="translations[{{ $languages->first()->id }}][name]"]').val();
            if (!nameEn || nameEn.trim() === '') {
                alert('{{ trans("vendor::vendor.name_required_all_languages") }}');
                isValid = false;
            }
            
            // Check activities selection
            const activities = $('#activities').val();
            if (!activities || activities.length === 0) {
                alert('{{ trans("vendor::vendor.please_select_at_least_one_activity") }}');
                isValid = false;
            }
            
            return isValid;
        }
        
        return true;
    }

    // Form submission
    $('#vendorForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateCurrentStep()) {
            return false;
        }
        
        LoadingOverlay.show();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    LoadingOverlay.showSuccess(
                        response.message || '{{ trans("vendor::vendor.vendor_created_successfully") }}',
                        '{{ trans("vendor::vendor.redirecting") }}'
                    );
                    setTimeout(function() {
                        window.location.href = response.redirect || '{{ route("admin.vendors.index") }}';
                    }, 1500);
                } else {
                    LoadingOverlay.hide();
                    alert(response.message || '{{ trans("vendor::vendor.an_error_occurred") }}');
                }
            },
            error: function(xhr) {
                LoadingOverlay.hide();
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '{{ trans("vendor::vendor.validation_errors") }}:\n';
                    Object.keys(errors).forEach(function(key) {
                        errorMessage += '- ' + errors[key][0] + '\n';
                    });
                    alert(errorMessage);
                } else {
                    alert('{{ trans("vendor::vendor.an_error_occurred") }}');
                }
            }
        });
    });
});
</script>
@endpush
