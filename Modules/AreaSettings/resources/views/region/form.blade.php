@extends('layout.app')


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::region.regions_management'), 'url' => route('admin.area-settings.regions.index')],
                    ['title' => isset($region) ? __('areasettings::region.edit_region') : __('areasettings::region.create_region')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($region) ? __('areasettings::region.edit_region') : __('areasettings::region.create_region') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="regionForm" method="POST" action="{{ isset($region) ? route('admin.area-settings.regions.update', $region->id) : route('admin.area-settings.regions.store') }}">
                            @csrf
                            @if(isset($region))
                                @method('PUT')
                            @endif
                            <div class="row">
                                <!-- Translation Fields -->
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name_{{ $language->id }}" class="form-label w-100 @if($language->rtl) text-end @else text-start @endif">
                                                @if($language->code == 'en')
                                                    {{ __('areasettings::region.name') }} ({{ $language->name }})
                                                @elseif($language->code == 'ar')
                                                    الاسم بالعربية
                                                @endif
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="name_{{ $language->id }}" name="translations[{{ $language->id }}][name]" value="{{ old('translations.' . $language->id . '.name', isset($region) ? $region->translations->where('lang_id', $language->id)->first()->lang_value ?? '' : '') }}" {{ $language->rtl ? 'dir=rtl' : '' }}>
                                            @error('translations.' . $language->id . '.name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="city_id" class="form-label">{{ __('areasettings::region.city') }} <span class="text-danger">*</span></label>
                                        <select name="city_id" id="city_id" class="form-control select2">
                                            <option value="">{{ __('areasettings::region.select_city') }}</option>
                                            @foreach($cities as $city)
                                                <option value="{{ $city['id'] }}" {{ (isset($region) && $region->city_id == $city['id']) ? 'selected' : '' }}>
                                                    {{ $city['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('city_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="active" class="form-label">{{ __('areasettings::region.status') }}</label>
                                        <select name="active" id="active" class="form-control">
                                            <option value="1" {{ (isset($region) && $region->active) || !isset($region) ? 'selected' : '' }}>{{ __('areasettings::region.active') }}</option>
                                            <option value="0" {{ (isset($region) && !$region->active) ? 'selected' : '' }}>{{ __('areasettings::region.inactive') }}</option>
                                        </select>
                                        @error('active')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-group d-flex justify-content-end mt-4">
                                        <a href="{{ route('admin.area-settings.regions.index') }}" class="btn btn-light btn-default btn-squared text-capitalize me-2">
                                            <i class="uil uil-arrow-left"></i> {{ __('areasettings::region.back_to_list') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize">
                                            <i class="uil uil-check"></i> {{ isset($region) ? __('areasettings::region.update_region') : __('areasettings::region.create_region') }}
                                        </button>
                                    </div>
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
    // Setup AJAX with CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2
    $('.select2').select2({
        placeholder: "{{ __('areasettings::region.select_city') }}",
        allowClear: true
    });

    // Form submission
    $('#regionForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Form submitted via AJAX');
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading overlay
        console.log('Showing loading overlay...');
        if (typeof window.showLoadingOverlay === 'function') {
            window.showLoadingOverlay();
        } else {
            console.warn('showLoadingOverlay function not found');
        }
        
        // Disable submit button
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            dataType: 'json',
            success: function(response) {
                // Hide loading overlay
                if (typeof window.hideLoadingOverlay === 'function') {
                    window.hideLoadingOverlay();
                }
                
                if (response.success) {
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    
                    // Redirect after a short delay
                    setTimeout(function() {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.href = '{{ route("admin.area-settings.regions.index") }}';
                        }
                    }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || '{{ __("areasettings::region.error_occurred") }}');
                    } else {
                        alert(response.message || '{{ __("areasettings::region.error_occurred") }}');
                    }
                    submitBtn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                // Hide loading overlay
                if (typeof window.hideLoadingOverlay === 'function') {
                    window.hideLoadingOverlay();
                }
                
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '{{ __("areasettings::region.validation_errors") }}:\n';
                    Object.keys(errors).forEach(function(key) {
                        errorMessage += '- ' + errors[key][0] + '\n';
                    });
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('{{ __("areasettings::region.error_occurred") }}');
                    } else {
                        alert('{{ __("areasettings::region.error_occurred") }}');
                    }
                }
                
                // Re-enable submit button
                submitBtn.prop('disabled', false);
            }
        });
        
        return false; // Ensure form doesn't submit normally
    });
});
</script>
@endpush