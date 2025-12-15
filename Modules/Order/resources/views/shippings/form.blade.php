@extends('layout.app')
@section('title', (isset($shipping)) ? trans('shipping.edit_shipping') : trans('shipping.create_shipping'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('shipping.shipping_management'), 'url' => route('admin.shippings.index')],
                    ['title' => isset($shipping) ? trans('shipping.edit_shipping') : trans('shipping.create_shipping')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($shipping) ? trans('shipping.edit_shipping') : trans('shipping.create_shipping') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer" class="mb-2"></div>

                        <form id="shippingForm"
                              action="{{ isset($shipping) ? route('admin.shippings.update', $shipping->id) : route('admin.shippings.store') }}"
                              method="POST">
                            @csrf
                            @if(isset($shipping))
                                @method('PUT')
                            @endif

                            {{-- Dynamic Language Fields for Name --}}
                            <x-multilingual-input
                                name="name"
                                label="Name"
                                labelAr="الاسم"
                                placeholder="Enter shipping name"
                                placeholderAr="أدخل اسم الشحن"
                                :required="true"
                                :languages="$languages"
                                :model="$shipping ?? null"
                            />

                            <div class="row">
                                {{-- Cost Field --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('shipping.cost') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                               step="0.01"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('cost') is-invalid @enderror"
                                               id="cost"
                                               name="cost"
                                               value="{{ old('cost', $shipping->cost ?? '') }}"
                                               placeholder="0.00"
                                               required>
                                        @error('cost')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Status Field --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('shipping.status') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="active"
                                                       name="active"
                                                       value="1"
                                                       {{ old('active', $shipping->active ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Country Field --}}
                                <div class="col-md-4 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('shipping.country') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="country_id" id="country_id" class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select @error('country_id') is-invalid @enderror" required>
                                            <option value="">{{ trans('common.select') }}</option>
                                            @foreach(\Modules\AreaSettings\app\Models\Country::all() as $country)
                                                <option value="{{ $country->id }}" {{ old('country_id', $shipping->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- City Field --}}
                                <div class="col-md-4 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('shipping.city') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="city_id" id="city_id" class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select @error('city_id') is-invalid @enderror" data-current-value="{{ old('city_id', $shipping->city_id ?? '') }}" required>
                                            <option value="">{{ trans('common.select') }}</option>
                                            @if(isset($shipping))
                                                @foreach(\Modules\AreaSettings\app\Models\City::where('country_id', $shipping->country_id)->get() as $city)
                                                    <option value="{{ $city->id }}" {{ old('city_id', $shipping->city_id) == $city->id ? 'selected' : '' }}>
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('city_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Category Field --}}
                                <div class="col-md-4 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('shipping.category') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="category_id" class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select @error('category_id') is-invalid @enderror" required>
                                            <option value="">{{ trans('common.select') }}</option>
                                            @foreach(\Modules\CategoryManagment\app\Models\Category::all() as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $shipping->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Form Actions --}}
                            <div class="row mt-30">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.shippings.index') }}" class="btn btn-light btn-default btn-squared">
                                            <i class="uil uil-arrow-left me-1"></i>
                                            {{ trans('main.cancel') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-squared" id="submitBtn">
                                            <i class="uil uil-check me-1"></i>
                                            {{ isset($shipping) ? trans('common.update') : trans('common.create') }}
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

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Load cities when country changes
            $('#country_id').on('change', function() {
                let countryId = $(this).val();
                let citySelect = $('#city_id');
                let currentCityId = citySelect.data('current-value');

                citySelect.html('<option value="">{{ trans('common.loading') }}...</option>');

                if (countryId) {
                    $.ajax({
                        url: '{{ route('api.area.cities.by-country', '__id__') }}'.replace('__id__', countryId),
                        type: 'GET',
                        success: function(response) {
                            citySelect.html('<option value="">{{ trans('common.select') }}</option>');
                            if (response.data) {
                                response.data.forEach(city => {
                                    citySelect.append(`<option value="${city.id}">${city.name}</option>`);
                                });
                            }
                            // Select the current city if in edit mode
                            if (currentCityId) {
                                citySelect.val(currentCityId).trigger('change');
                            }
                        },
                        error: function() {
                            citySelect.html('<option value="">{{ trans('common.error') }}</option>');
                        }
                    });
                } else {
                    citySelect.html('<option value="">{{ trans('common.select') }}</option>');
                }
            });

            // Trigger change on page load if country is selected
            if ($('#country_id').val()) {
                $('#country_id').trigger('change');
            }

            // Form submission with loading overlay
            $('#shippingForm').on('submit', function(e) {
                e.preventDefault();

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '{{ isset($shipping) ? trans('main.updating') : trans('main.creating') }}',
                        subtext: '{{ trans('main.please wait') }}'
                    });
                }

                const formData = new FormData(this);
                const url = $(this).attr('action');

                // Always use POST for AJAX requests, Laravel will handle method spoofing via _method field
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.showSuccess(
                                    response.message,
                                    '{{ trans('main.redirecting') }}'
                                );
                            }

                            setTimeout(function() {
                                window.location.href = '{{ route('admin.shippings.index') }}';
                            }, 1500);
                        } else {
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }
                            showAlert('danger', response.message);
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul class="mb-0">';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul>';
                            showAlert('danger', errorHtml);
                        } else {
                            const message = xhr.responseJSON?.message || '{{ trans('shipping.error_creating') }}';
                            showAlert('danger', message);
                        }
                    }
                });
            });

            // Alert function
            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#alertContainer').html(alertHtml);
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        });
    </script>
    @endpush
@endsection

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay
        :loadingText="isset($shipping) ? trans('main.updating') : trans('main.creating')"
        :loadingSubtext="trans('main.please wait')"
    />
@endpush
