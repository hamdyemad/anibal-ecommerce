@extends('layout.app')

@push('styles')
<!-- Select2 CSS loaded via Vite -->
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::city.cities_management'), 'url' => route('admin.area-settings.cities.index')],
                    ['title' => isset($city) ? __('areasettings::city.edit_city') : __('areasettings::city.add_city')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">{{ isset($city) ? __('areasettings::city.edit_city') : __('areasettings::city.add_city') }}</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ isset($city) ? route('admin.area-settings.cities.update', $city->id) : route('admin.area-settings.cities.store') }}" id="cityForm">
                            @csrf
                            @if(isset($city))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <!-- Country Selection -->
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label for="country_id" class="form-label">{{ __('areasettings::city.country') }} <span class="text-danger">*</span></label>
                                        <select name="country_id" id="country_id" class="form-control select2">
                                            <option value="">{{ __('areasettings::city.select_country') }}</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country['id'] }}" {{ (isset($city) && $city->country_id == $country->id) ? 'selected' : '' }}>
                                                    {{ $country['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Active Status -->
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label for="active" class="form-label">{{ __('areasettings::city.status') }}</label>
                                        <select name="active" id="active" class="form-control">
                                            <option value="1" {{ (isset($city) && $city->active) || !isset($city) ? 'selected' : '' }}>{{ __('areasettings::city.active') }}</option>
                                            <option value="0" {{ (isset($city) && !$city->active) ? 'selected' : '' }}>{{ __('areasettings::city.inactive') }}</option>
                                        </select>
                                        @error('active')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="translations_{{ $language->id }}_name" class="form-label w-100 @if($language->rtl) text-end @else text-start @endif">
                                                @if($language->code == 'en')
                                                {{ __('areasettings::city.name') }} ({{ $language->name }}) 
                                                @elseif($language->code == 'ar')
                                                    الاسم بالعربية
                                                @endif
                                                
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                name="translations[{{ $language->id }}][name]" 
                                                id="translations_{{ $language->id }}_name"
                                                class="form-control" 
                                                value="{{ isset($city) ? $city->getTranslation('name', $language->code) : old('translations.'.$language->id.'.name') }}"
                                                {{ $language->rtl ? 'dir=rtl' : '' }}
                                            >
                                            @error('translations.'.$language->id.'.name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-12">
                                    <div class="form-group d-flex justify-content-end mt-4">
                                        <a href="{{ route('admin.area-settings.cities.index') }}" class="btn btn-light btn-default btn-squared text-capitalize me-2">
                                            <i class="uil uil-arrow-left"></i> {{ __('areasettings::city.back_to_list') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize">
                                            <i class="uil uil-check"></i> {{ isset($city) ? __('areasettings::city.update_city') : __('areasettings::city.create_city') }}
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
        placeholder: "{{ __('areasettings::city.select_country') }}",
        allowClear: true
    });

    // Form submission
    $('#cityForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading overlay
        if (typeof window.showLoadingOverlay === 'function') {
            window.showLoadingOverlay();
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
                            window.location.href = '{{ route("admin.area-settings.cities.index") }}';
                        }
                    }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || '{{ __("areasettings::city.error_occurred") }}');
                    } else {
                        alert(response.message || '{{ __("areasettings::city.error_occurred") }}');
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
                    let errorMessage = '{{ __("areasettings::city.validation_errors") }}:\n';
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
                        toastr.error('{{ __("areasettings::city.error_occurred") }}');
                    } else {
                        alert('{{ __("areasettings::city.error_occurred") }}');
                    }
                }
                
                // Re-enable submit button
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
