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
                    ['title' => __('areasettings::subregion.subregions_management'), 'url' => route('admin.area-settings.subregions.index')],
                    ['title' => isset($subregion) ? __('areasettings::subregion.edit_subregion') : __('areasettings::subregion.add_subregion')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ isset($subregion) ? __('areasettings::subregion.edit_subregion') : __('areasettings::subregion.add_subregion') }}</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ isset($subregion) ? route('admin.area-settings.subregions.update', $subregion->id) : route('admin.area-settings.subregions.store') }}" id="subregionForm">
                            @csrf
                            @if(isset($subregion))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <!-- Translations -->
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="translations_{{ $language->id }}_name" class="form-label w-100 @if($language->code == 'ar') text-end @endif">
                                                @if($language->rtl)
                                                    الاسم باللغة
                                                @else
                                                    {{ __('areasettings::subregion.name') }} ({{ $language->name }})
                                                @endif
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                name="translations[{{ $language->id }}][name]" 
                                                id="translations_{{ $language->id }}_name"
                                                class="form-control" 
                                                value="{{ isset($subregion) ? $subregion->getTranslation('name', $language->code) : old('translations.'.$language->id.'.name') }}"
                                                {{ $language->rtl ? 'dir=rtl' : '' }}
                                            >
                                            @error('translations.'.$language->id.'.name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                                <!-- Region Selection -->
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label for="region_id" class="form-label">{{ __('areasettings::subregion.region') }} <span class="text-danger">*</span></label>
                                        <select name="region_id" id="region_id" class="form-control select2">
                                            <option value="">{{ __('areasettings::subregion.select_region') }}</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region['id'] }}" {{ isset($subregion) && $subregion->region_id == $region['id'] ? 'selected' : '' }}>
                                                    {{ $region['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('region_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Active Status -->
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label for="active" class="form-label">{{ __('areasettings::subregion.status') }}</label>
                                        <select name="active" id="active" class="form-control">
                                            <option value="1" {{ (isset($subregion) && $subregion->active) || !isset($subregion) ? 'selected' : '' }}>{{ __('areasettings::subregion.active') }}</option>
                                            <option value="0" {{ (isset($subregion) && !$subregion->active) ? 'selected' : '' }}>{{ __('areasettings::subregion.inactive') }}</option>
                                        </select>
                                        @error('active')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mt-4 d-flex justify-content-end">
                                        <a href="{{ route('admin.area-settings.subregions.index') }}" class="btn btn-light btn-default btn-squared text-capitalize me-2">
                                            <i class="uil uil-arrow-left"></i> {{ __('areasettings::subregion.back_to_list') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize">
                                            <i class="uil uil-check"></i> {{ __('areasettings::subregion.save') }}
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
    // Initialize Select2
    $('.select2').select2({
        allowClear: true
    });

    // Load cities when country changes
    $('#country_id').on('change', function() {
        const countryId = $(this).val();
        const citySelect = $('#city_id');
        const regionSelect = $('#region_id');
        
        citySelect.empty().append('<option value="">{{ __("areasettings::subregion.select_city") }}</option>');
        regionSelect.empty().append('<option value="">{{ __("areasettings::subregion.select_region") }}</option>');
        
        if (countryId) {
            $.ajax({
                url: '{{ route("admin.area-settings.cities.by-country", ":id") }}'.replace(':id', countryId),
                type: 'GET',
                success: function(cities) {
                    cities.forEach(function(city) {
                        const cityName = city.translations.find(t => t.lang_key === 'name' && t.lang_id == {{ $languages->first()->id }})?.lang_value || city.id;
                        citySelect.append(`<option value="${city.id}">${cityName}</option>`);
                    });
                },
                error: function(xhr) {
                    console.error('Error loading cities:', xhr);
                    alert('Error loading cities. Please try again.');
                }
            });
        }
    });

    // Load regions when city changes
    $('#city_id').on('change', function() {
        const cityId = $(this).val();
        const regionSelect = $('#region_id');
        
        regionSelect.empty().append('<option value="">{{ __("areasettings::subregion.select_region") }}</option>');
        
        if (cityId) {
            $.ajax({
                url: '{{ route("admin.area-settings.regions.by-city", ":id") }}'.replace(':id', cityId),
                type: 'GET',
                success: function(regions) {
                    regions.forEach(function(region) {
                        const regionName = region.translations.find(t => t.lang_key === 'name' && t.lang_id == {{ $languages->first()->id }})?.lang_value || region.id;
                        regionSelect.append(`<option value="${region.id}">${regionName}</option>`);
                    });
                },
                error: function(xhr) {
                    console.error('Error loading regions:', xhr);
                    alert('Error loading regions. Please try again.');
                }
            });
        }
    });

    // Form submission
    $('#subregionForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading overlay
        LoadingOverlay.show();
        
        // Disable submit button
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    LoadingOverlay.showSuccess(
                        response.message || '{{ __("areasettings::subregion.subregion_created") }}',
                        '{{ __("common.redirecting") ?? "Redirecting..." }}'
                    );
                    setTimeout(function() {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.href = '{{ route("admin.area-settings.subregions.index") }}';
                        }
                    }, 1500);
                } else {
                    LoadingOverlay.hide();
                    alert(response.message || '{{ __("areasettings::subregion.error_occurred") }}');
                    submitBtn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                LoadingOverlay.hide();
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '{{ __("areasettings::subregion.validation_errors") }}:\n';
                    Object.keys(errors).forEach(function(key) {
                        errorMessage += '- ' + errors[key][0] + '\n';
                    });
                    alert(errorMessage);
                } else {
                    alert('{{ __("areasettings::subregion.error_occurred") }}');
                }
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
