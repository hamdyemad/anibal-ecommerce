@extends('layout.app')

@section('title', __('catalogmanagement::product.edit_product'))

@push('styles')
@vite(['Modules/CatalogManagement/resources/assets/scss/product-form.scss'])
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('catalogmanagement::product.products_management'), 'url' => route('admin.products.index')],
                ['title' => __('catalogmanagement::product.edit_product')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card product-form">
                <div class="card-header">
                    <h4 class="card-title">{{ __('catalogmanagement::product.edit_product') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Wizard Navigation -->
                    <x-wizard :steps="[
                        __('common.basic_information'),
                        __('common.details'),
                        __('common.variant_configurations'),
                        __('common.seo')
                    ]" :currentStep="1" />

                    <!-- Validation Alerts Container -->
                    <div id="validation-alerts-container" class="mb-3"></div>

                    <!-- Form -->
                    <form id="productEditForm" method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" novalidate onkeydown="return event.key != 'Enter';">
                        @csrf
                        @method('PUT')

                        <!-- Step 1: Basic Information -->
                        <div class="wizard-step-content active" data-step="1">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-info-circle"></i>
                                        {{ __('catalogmanagement::product.product_details') }}
                                    </h5>
                            <div class="row">
                                <!-- Product Titles -->
                                @foreach($languages as $language)
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('catalogmanagement::product.title') }} ({{ $language->name }})</label>
                                        <input type="text" name="translations[{{ $language->id }}][title]" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               value="{{ $product->product->getTranslation('title', $language->code) ?? '' }}" required>
                                    </div>
                                </div>
                                @endforeach

                                <!-- SKU and Points -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="sku" class="form-label">{{ __('catalogmanagement::product.sku') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="sku" id="sku" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="{{ __('catalogmanagement::product.sku') }}" value="{{ isset($product) ? $product->sku : '' }}">
                                        <div class="error-message text-danger" id="error-sku" style="display: none;"></div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="points" class="form-label">{{ __('catalogmanagement::product.points') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="points" id="points" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" value="{{ isset($product) ? $product->points : 0 }}" placeholder="Enter points" required>
                                        <div class="error-message text-danger" id="error-points" style="display: none;"></div>
                                    </div>
                                </div>

                                <!-- Status & Featured -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label d-block">{{ __('catalogmanagement::product.status') }}</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1"
                                            {{ ($product->product ? $product->product->is_active : $product->is_active) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label d-block">{{ __('catalogmanagement::product.featured') }}</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1"
                                            {{ ($product->product ? $product->product->is_featured : $product->is_featured) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Organization -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-4">
                                    <i class="uil uil-sitemap"></i>
                                    {{ __('common.organization') }}
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="brand_id" class="form-label">{{ __('catalogmanagement::product.brand') }} <span class="text-danger">*</span></label>
                                            <select name="brand_id" id="brand_id" class="form-control select2">
                                                <option value="">{{ __('common.select_option') }}</option>
                                                @foreach($brands as $brand)
                                                <option value="{{ $brand['id'] }}" {{ ($product->product->brand_id == $brand['id']) ? 'selected' : '' }}>
                                                    {{ $brand['name'] }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="vendor_id" class="form-label">{{ __('catalogmanagement::product.vendor') }} <span class="text-danger">*</span></label>
                                            <select name="vendor_id" id="vendor_id" class="form-control select2">
                                                <option value="">{{ __('common.select_option') }}</option>
                                                @foreach($vendors as $vendor)
                                                <option value="{{ $vendor['id'] }}" {{ ($product->vendor_id == $vendor['id']) ? 'selected' : '' }}>
                                                    {{ $vendor->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="department_id" class="form-label">{{ __('catalogmanagement::product.department') }} <span class="text-danger">*</span></label>
                                            <select name="department_id" id="department_id" class="form-control select2">
                                                <option value="">{{ __('common.select_option') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="category_id" class="form-label">{{ __('catalogmanagement::product.category') }} <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-control select2">
                                                <option value="">{{ __('common.select_option') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="sub_category_id" class="form-label">{{ __('catalogmanagement::product.sub_category') }}</label>
                                            <select name="sub_category_id" id="sub_category_id" class="form-control select2">
                                                <option value="">{{ __('common.select_option') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Logistics & Taxes -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-4">
                                    <i class="uil uil-truck"></i>
                                    {{ __('common.logistics') }}
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="tax_id" class="form-label">{{ __('catalogmanagement::product.tax') }} <span class="text-danger">*</span></label>
                                            <select name="tax_id" id="tax_id" class="form-control select2">
                                                <option value="">{{ __('common.select_option') }}</option>
                                                @foreach($taxes as $tax)
                                                <option value="{{ $tax['id'] }}" {{ ($product->tax_id == $tax['id']) ? 'selected' : '' }}>
                                                    {{ $tax['name'] }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="max_per_order" class="form-label">{{ __('catalogmanagement::product.max_per_order') }} <span class="text-danger">*</span></label>
                                            <input type="number" name="max_per_order" id="max_per_order" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="1" placeholder="Enter max per order" value="{{ isset($product) ? ($product->max_per_order ?? ($product->product ? $product->product->max_per_order : null)) ?? '' : '' }}" required>
                                            <div class="error-message text-danger" id="error-max_per_order" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4: Product Tags -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-4">
                                    <i class="uil uil-tag-alt"></i>
                                    {{ __('common.tags') }}
                                </h5>
                                <div class="row">
                                    @foreach($languages as $language)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="tags_{{ $language->code }}" class="form-label w-100">
                                                {{ __('common.tags') }} ({{ strtoupper($language->code) }})
                                            </label>
                                            <div>
                                                <x-tags-input
                                                    name="translations[{{ $language->id }}][tags]"
                                                    :value="isset($product) ? (isset($product->product) && method_exists($product->product, 'getTagsString') ? $product->product->getTagsString($language->code) : (method_exists($product, 'getTagsString') ? $product->getTagsString($language->code) : '')) : ''"
                                                    placeholder="{{ app()->getLocale() == 'ar' ? 'اكتب وسم واضغط انتر' : 'Type a tag and press Enter...' }}"
                                                    rtl-placeholder="اكتب وسم واضغط انتر"
                                                    language="{{ $language->code }}"
                                                    :allow-duplicates="true"
                                                    theme="primary"
                                                    size="md"
                                                    id="tags_{{ $language->code }}"
                                                    dir="{{ (app()->getLocale() == 'ar' && $language->code == 'ar') ? 'rtl' : 'ltr' }}"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Additional Details -->
                        <div class="wizard-step-content" data-step="2">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-file-alt"></i>
                                        {{ __('catalogmanagement::product.additional_details') }}
                                    </h5>
                            <div class="row">
                                @foreach($languages as $language)
                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('catalogmanagement::product.description') }} ({{ $language->name }})</label>
                                        <textarea name="translations[{{ $language->id }}][description]" class="form-control" rows="4">{{ $product->product->getTranslation('description', $language->code) ?? '' }}</textarea>
                                    </div>
                                </div>

                                <!-- Summary -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('catalogmanagement::product.summary') }} ({{ $language->name }})</label>
                                        <textarea name="translations[{{ $language->id }}][summary]" class="form-control" rows="3">{{ $product->product->getTranslation('summary', $language->code) ?? '' }}</textarea>
                                    </div>
                                </div>

                                <!-- Features -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('catalogmanagement::product.features') }} ({{ $language->name }})</label>
                                        <textarea name="translations[{{ $language->id }}][features]" class="form-control" rows="3">{{ $product->product->getTranslation('features', $language->code) ?? '' }}</textarea>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Images -->
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('catalogmanagement::product.images') }}</label>
                                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                        <small class="text-muted">{{ __('catalogmanagement::product.images_help') }}</small>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Variant Configuration -->
                        <div class="wizard-step-content" data-step="3">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-layers"></i>
                                        {{ __('catalogmanagement::product.variant_configurations') }}
                                    </h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('catalogmanagement::product.configuration_type') }}</label>
                                        <select name="configuration_type" id="configuration_type" class="form-control" required>
                                            <option value="simple" {{ ($product->configuration_type == 'simple') ? 'selected' : '' }}>{{ __('catalogmanagement::product.simple_product') }}</option>
                                            <option value="variants" {{ ($product->configuration_type == 'variants') ? 'selected' : '' }}>{{ __('catalogmanagement::product.variant_product') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Simple Product Section -->
                                <div id="simple-product-section" style="{{ ($product->configuration_type == 'simple') ? '' : 'display: none;' }}">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('catalogmanagement::product.sku') }}</label>
                                            <input type="text" name="sku" class="form-control" value="{{ $product->variants->first()->sku ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('catalogmanagement::product.price') }}</label>
                                            <input type="number" name="price" class="form-control" step="0.01" value="{{ $product->variants->first()->price ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Variants Section -->
                                <div id="variants-section" style="{{ ($product->configuration_type == 'variants') ? '' : 'display: none;' }}">
                                    <div class="variants-container">
                                        <!-- Variants will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: SEO Meta -->
                        <div class="wizard-step-content" data-step="4">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-search"></i>
                                        {{ __('catalogmanagement::product.seo_meta') }}
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('catalogmanagement::product.meta_title') }} ({{ $language->name }})</label>
                                                <input type="text" name="translations[{{ $language->id }}][meta_title]" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                       value="{{ $product->product->getTranslation('meta_title', $language->code) ?? '' }}">
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('catalogmanagement::product.meta_description') }} ({{ $language->name }})</label>
                                                <textarea name="translations[{{ $language->id }}][meta_description]" class="form-control ih-medium ip-gray radius-xs b-light px-15" rows="3">{{ $product->product->getTranslation('meta_description', $language->code) ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="wizard-navigation d-flex justify-content-between mt-4">
                            <button type="button" id="prevBtn" class="btn btn-light btn-lg" style="display: none;">
                                <i class="uil uil-arrow-left"></i>
                                {{ __('common.previous') }}
                            </button>
                            <button type="button" id="nextBtn" class="btn btn-primary btn-lg">
                                {{ __('common.next') }}
                                <i class="uil uil-arrow-right"></i>
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-success btn-lg" style="display: none;">
                                <i class="uil uil-check"></i>
                                {{ __('catalogmanagement::product.update_product') }}
                            </button>
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
            console.log('Document ready - script started');
            let currentStep = 1;
            const totalSteps = 4;

            // Wizard Navigation
            function showStep(step) {
                $('.wizard-step-content').removeClass('active');
                $('.wizard-step-content[data-step="' + step + '"]').addClass('active');

                // Update wizard component
                $('.wizard-step').removeClass('active completed');
                $('.wizard-step').each(function(index) {
                    const stepNum = index + 1;
                    if (stepNum < step) {
                        $(this).addClass('completed');
                    } else if (stepNum === step) {
                        $(this).addClass('active');
                    }
                });

                // Update navigation buttons
                $('#prevBtn').toggle(step > 1);
                $('#nextBtn').toggle(step < totalSteps);
                $('#submitBtn').toggle(step === totalSteps);
            }

            // Next button click
            $('#nextBtn').click(function() {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            // Previous button click
            $('#prevBtn').click(function() {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            // Configuration type change
            $('#configuration_type').change(function() {
                const type = $(this).val();
                if (type === 'simple') {
                    $('#simple-product-section').show();
                    $('#variants-section').hide();
                } else {
                    $('#simple-product-section').hide();
                    $('#variants-section').show();
                }
            });


            // Load departments based on vendor
            function loadDepartments() {
                console.log('sssssssssssssssssss - loadDepartments called');
                const vendorId = {{ $product->vendor_id ?? 'null' }};
                console.log('vendorId:', vendorId);
                console.log('vendorId type:', typeof vendorId);
                console.log('aaaaaaaaaaaaaaaaaa')

                if (vendorId) {
                    $.ajax({
                        url: '/api/departments?vendor_id=' + vendorId,
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log(response)
                            const departmentSelect = $('#department_id');
                            departmentSelect.empty().append('<option value="">{{ __("common.select_option") }}</option>');

                            if (response.status && response.data) {
                                response.data.forEach(function(department) {
                                    const selected = ({{ $product->product->department_id ?? 'null' }} == department.id) ? 'selected' : '';
                                    departmentSelect.append(`<option value="${department.id}" ${selected}>${department.name}</option>`);
                                });

                                // If department is selected, load categories
                                if ({{ $product->product->department_id ?? 'null' }}) {
                                    loadCategories({{ $product->product->department_id ?? 'null' }});
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading departments:', xhr);
                        }
                    });
                }
            }

            // Load categories based on department
            function loadCategories(departmentId) {
                if (!departmentId) return;

                $.ajax({
                    url: '/api/categories?department_id=' + departmentId,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        const categorySelect = $('#category_id');
                        categorySelect.empty().append('<option value="">{{ __("common.select_option") }}</option>');

                        if (response.status && response.data) {
                            response.data.forEach(function(category) {
                                const selected = ({{ $product->product->category_id ?? 'null' }} == category.id) ? 'selected' : '';
                                categorySelect.append(`<option value="${category.id}" ${selected}>${category.name}</option>`);
                            });

                            // If category is selected, load subcategories
                            if ({{ $product->product->category_id ?? 'null' }}) {
                                loadSubCategories({{ $product->product->category_id ?? 'null' }});
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading categories:', xhr);
                    }
                });
            }

            // Load subcategories based on category
            function loadSubCategories(categoryId) {
                if (!categoryId) return;

                $.ajax({
                    url: '/api/subcategories?category_id=' + categoryId,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        const subCategorySelect = $('#sub_category_id');
                        subCategorySelect.empty().append('<option value="">{{ __("common.select_option") }}</option>');

                        if (response.status && response.data) {
                            response.data.forEach(function(subcategory) {
                                const selected = ({{ $product->product->sub_category_id ?? 'null' }} == subcategory.id) ? 'selected' : '';
                                subCategorySelect.append(`<option value="${subcategory.id}" ${selected}>${subcategory.name}</option>`);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading subcategories:', xhr);
                    }
                });
            }

            loadDepartments();

            function departmentOnChange() {
                // Department change - load categories
                $('#department_id').change(function() {
                    const departmentId = $(this).val();
                    const categorySelect = $('#category_id');
                    const subCategorySelect = $('#sub_category_id');

                    // Clear categories and subcategories
                    categorySelect.empty().append('<option value="">{{ __("catalogmanagement::product.select_category") }}</option>');
                    subCategorySelect.empty().append('<option value="">{{ __("catalogmanagement::product.select_sub_category") }}</option>');

                    if (departmentId) {
                        $.ajax({
                            url: '/admin/categories/by-department',
                            method: 'GET',
                            data: { department_id: departmentId },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status && response.data) {
                                    response.data.forEach(function(category) {
                                        categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error('Error loading categories:', xhr);
                            }
                        });
                    }
                });
            }
            // departmentOnChange();

            function categoryOnChange() {
                // Category change - load subcategories
                $('#category_id').change(function() {
                    const categoryId = $(this).val();
                    const subCategorySelect = $('#sub_category_id');

                    subCategorySelect.empty().append('<option value="">{{ __("catalogmanagement::product.select_sub_category") }}</option>');

                    if (categoryId) {
                        $.ajax({
                            url: '/admin/subcategories/by-category',
                            method: 'GET',
                            data: { category_id: categoryId },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status && response.data) {
                                    response.data.forEach(function(subcategory) {
                                        subCategorySelect.append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error('Error loading subcategories:', xhr);
                            }
                        });
                    }
                });
            }

            // categoryOnChange();

            // Form submission
            $('#productEditForm').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const serializedData = $(this).serialize();

                console.log('Form Data:', serializedData);

                // Show loading
                $('#submitBtn').prop('disabled', true).text('{{ __("common.updating") }}...');

                $.ajax({
                    url: '{{ route("admin.products.update", $product->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            toastr.success(response.message || '{{ __("catalogmanagement::product.product_updated_successfully") }}');

                            // Redirect after delay
                            setTimeout(function() {
                                window.location.href = '{{ route("admin.products.index") }}';
                            }, 1500);
                        } else {
                            toastr.error(response.message || '{{ __("common.error_occurred") }}');
                            $('#submitBtn').prop('disabled', false).text('{{ __("catalogmanagement::product.update_product") }}');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = '{{ __("common.validation_errors") }}:\n';

                            Object.keys(errors).forEach(function(key) {
                                errorMessage += '- ' + errors[key][0] + '\n';
                            });

                            toastr.error(errorMessage);
                        } else {
                            toastr.error('{{ __("common.error_occurred") }}');
                        }

                        $('#submitBtn').prop('disabled', false).text('{{ __("catalogmanagement::product.update_product") }}');
                    }
                });
            });

            // Initialize wizard
            showStep(1);

            // Handle wizard step clicks
            $(document).on('click', '.wizard-step', function() {
                const targetStep = parseInt($(this).data('step'));
                if (targetStep && targetStep !== currentStep) {
                    currentStep = targetStep;
                    showStep(currentStep);
                }
            });
        });
    </script>
@endpush
@endsection
