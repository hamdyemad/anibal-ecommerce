@extends('layout.app')
@section('title', isset($bundle) ? trans('catalogmanagement::bundle.edit_bundle') : trans('catalogmanagement::bundle.add_bundle'))

@push('styles')
    <style>
        /* Alert styling */
        .alert {
            border: none;
            border-radius: 8px;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('catalogmanagement::bundle.bundles_management'),
                        'url' => route('admin.bundles.index'),
                    ],
                    [
                        'title' => isset($bundle)
                            ? trans('catalogmanagement::bundle.edit_bundle')
                            : trans('catalogmanagement::bundle.add_bundle'),
                    ],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($bundle) ? trans('catalogmanagement::bundle.edit_bundle') : trans('catalogmanagement::bundle.add_bundle') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer" class="mb-2"></div>

                        <!-- Laravel Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="uil uil-exclamation-triangle me-2"></i>
                                    <strong>{{ __('common.validation_errors') }}</strong>
                                </div>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="bundleForm"
                            action="{{ isset($bundle) ? route('admin.bundles.update', $bundle->id) : route('admin.bundles.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($bundle))
                                @method('PUT')
                            @endif

                            {{-- Bundle Name Fields --}}
                            <x-multilingual-input name="name" :label="trans('catalogmanagement::bundle.name')" :labelAr="'اسم الحزمة'" :placeholder="trans('catalogmanagement::bundle.enter_name')"
                                :placeholderAr="'اسم الحزمة'" :languages="$languages" :model="$bundle ?? null" :required="true" />

                            {{-- Bundle Description Fields --}}
                            <x-multilingual-input name="description" :label="trans('catalogmanagement::bundle.description')" :labelAr="'الوصف'" :placeholder="trans('catalogmanagement::bundle.enter_description')"
                                :placeholderAr="'الوصف'" type="textarea" rows="3" :languages="$languages" :model="$bundle ?? null" />

                            {{-- SKU and Image --}}
                            <div class="row">
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="sku" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::bundle.sku') }} <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                            id="sku" name="sku"
                                            value="{{ old('sku', isset($bundle) ? $bundle->sku : '') }}" required>
                                        @error('sku')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="bundle_category_id" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::bundle.category') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="bundle_category_id" id="bundle_category_id" class="form-control select2" required>
                                            <option value="">{{ trans('catalogmanagement::bundle.select_category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('bundle_category_id', $bundle->bundle_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->getTranslation('name', 'en') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('bundle_category_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <x-image-upload id="bundle_image" name="image" :placeholder="trans('catalogmanagement::bundle.image')"
                                            :recommendedSize="trans('catalogmanagement::bundle.recommended_size')" :existingImage="isset($bundle) && $bundle->image
                                                ? asset('storage/' . $bundle->image)
                                                : null" aspectRatio="16:9" />
                                        @error('image')
                                            <div class="invalid-feedback d-block" style="display: block !important;">
                                                {{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('catalogmanagement::bundle.is_active') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" class="form-check-input" id="is_active"
                                                    name="is_active" value="1"
                                                    {{ old('is_active', $bundle->is_active ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @error('is_active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                            </div>
                            {{-- Vendor and Category Selection --}}
                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <div class="form-group">
                                        <label for="vendor_id" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::bundle.vendor') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="vendor_id" id="vendor_id" class="form-control select2" required>
                                            <option value="">{{ trans('catalogmanagement::bundle.select_vendor') }}</option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}"
                                                    {{ old('vendor_id', $bundle->vendor_id ?? '') == $vendor->id ? 'selected' : '' }}>
                                                    {{ $vendor->getTranslation('name', 'en') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vendor_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>





                            {{-- SEO Information Section --}}
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="p-0 fw-500 fw-bold">
                                        {{ trans('catalogmanagement::bundle.seo_information') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- SEO Title Fields --}}
                                        <x-multilingual-input name="seo_title" :label="trans('catalogmanagement::bundle.seo_title')" :labelAr="'عنوان ال SEO'"
                                            :placeholder="trans('catalogmanagement::bundle.enter_seo_title')" :placeholderAr="'عنوان ال SEO'" :languages="$languages" :model="$bundle ?? null" />

                                        {{-- SEO Description Fields --}}
                                        <x-multilingual-input name="seo_description" :label="trans('catalogmanagement::bundle.seo_description')" :labelAr="'وصف SEO'"
                                            :placeholder="trans('catalogmanagement::bundle.enter_seo_description')" :placeholderAr="'وصف SEO'" type="textarea" rows="3"
                                            :languages="$languages" :model="$bundle ?? null" />

                                        {{-- SEO Keywords Fields --}}
                                        <x-multilingual-input name="seo_keywords" :label="trans('catalogmanagement::bundle.seo_keywords')" :labelAr="'كلمات مفتاحية SEO'"
                                            :placeholder="trans('catalogmanagement::bundle.enter_seo_keywords')" :placeholderAr="'كلمات مفتاحية SEO'" :languages="$languages" :model="$bundle ?? null" />
                                    </div>
                                </div>
                            </div>

                            {{-- Form Actions --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" id="submitBtn" class="btn btn-primary btn-default btn-squared">
                                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                                            {{ isset($bundle) ? trans('catalogmanagement::bundle.update_bundle') : trans('catalogmanagement::bundle.create_bundle') }}
                                        </button>
                                        <a href="{{ route('admin.bundles.index') }}" class="btn btn-secondary btn-default btn-squared">
                                            {{ trans('catalogmanagement::bundle.cancel') }}
                                        </a>
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
            const form = $('#bundleForm');
            const submitBtn = $('#submitBtn');
            const alertContainer = $('#alertContainer');

            submitBtn.on('click', function(e) {
                e.preventDefault();
                const spinner = submitBtn.find('.spinner-border');
                spinner.removeClass('d-none');
                submitBtn.prop('disabled', true);

                const formData = new FormData(form[0]);

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        spinner.addClass('d-none');
                        submitBtn.prop('disabled', false);

                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message, 'Success');
                        }

                        setTimeout(function() {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        }, 1500);
                    },
                    error: function(xhr) {
                        spinner.addClass('d-none');
                        submitBtn.prop('disabled', false);

                        const response = xhr.responseJSON;
                        const errors = response.errors || {};

                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message || 'An error occurred', 'Error');
                        }

                        let errorHtml = "<div class='alert alert-danger alert-dismissible fade show d-block' role='alert'>";
                        errorHtml += "<div class='d-flex align-items-center'>";
                        errorHtml += "<i class='uil uil-exclamation-triangle me-2'></i>";
                        errorHtml += "<strong>Validation Errors</strong>";
                        errorHtml += "</div>";
                        errorHtml += "<ul class='mb-0 mt-2'>";

                        for (const key in errors) {
                            const errorMessages = errors[key];
                            if (Array.isArray(errorMessages)) {
                                errorMessages.forEach(function(msg) {
                                    errorHtml += "<li>" + msg + "</li>";
                                });
                            } else {
                                errorHtml += "<li>" + errorMessages + "</li>";
                            }
                        }

                        errorHtml += "</ul>";
                        errorHtml += "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
                        errorHtml += "</div>";

                        alertContainer.html(errorHtml);
                        window.scrollTo(0, 0);
                    }
                });
            });
        });
    </script>
@endpush
@endsection
