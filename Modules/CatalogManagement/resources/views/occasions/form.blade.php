@extends('layout.app')
@section('title', (isset($occasion)) ? trans('catalogmanagement::occasion.edit_occasion') : trans('catalogmanagement::occasion.add_occasion'))

@push('styles')
<style>
    .product-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    .product-card:hover {
        border-color: var(--color-primary);
        box-shadow: 0 4px 12px rgba(0,123,255,0.15);
    }
    .product-card.selected {
        border-color: #28a745;
        background-color: #f8fff8;
    }
    .product-card .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .product-card .product-checkbox {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .product-card .product-info h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }
    .product-card .product-info .product-meta {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::occasion.occasions_management'), 'url' => route('admin.occasions.index')],
                    ['title' => isset($occasion) ? trans('catalogmanagement::occasion.edit_occasion') : trans('catalogmanagement::occasion.add_occasion')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($occasion) ? trans('catalogmanagement::occasion.edit_occasion') : trans('catalogmanagement::occasion.add_occasion') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer" class="mb-2"></div>

                        <form id="occasionForm"
                              action="{{ isset($occasion) ? route('admin.occasions.update', $occasion->id) : route('admin.occasions.store') }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @if(isset($occasion))
                                @method('PUT')
                            @endif

                            {{-- Vendor Selection --}}
                            <div class="row">
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="vendor_id" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::occasion.vendor') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="vendor_id" id="vendor_id" class="form-control select2" required>
                                            <option value="">{{ trans('common.select') }}</option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->id }}" {{ old('vendor_id', $occasion->vendor_id ?? '') == $vendor->id ? 'selected' : '' }}>
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vendor_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                                                {{-- Activation Switcher --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('catalogmanagement::occasion.activation') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="is_active"
                                                       name="is_active"
                                                       value="1"
                                                       {{ old('is_active', $occasion->is_active ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @error('is_active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Product Variants Section --}}
                            <div class="row mt-30" id="variantsSection" style="display: none;">
                                <div class="col-12">
                                    <h6 class="mb-20 fw-500">{{ trans('catalogmanagement::occasion.product_variants') }}</h6>
                                </div>
                                <div class="col-12 mb-25">
                                    <div class="form-group">
                                        <label for="product_search" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::occasion.search_products') }}
                                        </label>
                                        <input type="text"
                                               id="product_search"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               placeholder="{{ trans('catalogmanagement::occasion.search_products_placeholder') }}">
                                        <small class="text-muted">{{ trans('catalogmanagement::occasion.search_products_help') }}</small>
                                    </div>

                                    {{-- Search Loading Indicator --}}
                                    <div id="products-loading" style="display: none;">
                                        <div class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">{{ trans('common.loading') }}...</span>
                                            </div>
                                            <p class="mt-2 mb-0 text-muted small">{{ trans('catalogmanagement::occasion.searching_products') }}...</p>
                                        </div>
                                    </div>

                                    {{-- Selected Products Alert --}}
                                    <div id="selected-products-alert" class="alert alert-success mt-3 d-block" style="display: none;">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div>
                                                <strong>{{ trans('catalogmanagement::occasion.selected_products') }}:</strong>
                                                <span id="selected-count">0</span> {{ trans('catalogmanagement::occasion.products_selected') }}
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="clear-selections">
                                                {{ trans('common.clear_all') }}
                                            </button>
                                        </div>
                                        <div id="selected-products-list" class="d-block"></div>
                                    </div>

                                    {{-- Products Container --}}
                                    <div id="products-container" class="mt-3">
                                        <div id="products-list"></div>
                                        <div id="no-products" class="text-center text-muted py-4" style="display: none;">
                                            {{ trans('catalogmanagement::occasion.no_products_found') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Occasion Name Fields --}}
                            <x-multilingual-input
                                name="name"
                                label="Name"
                                labelAr="الاسم"
                                placeholder="Enter occasion name"
                                placeholderAr="أدخل اسم المناسبة"
                                :required="true"
                                :languages="$languages"
                                :model="$occasion ?? null"
                            />

                            {{-- Occasion Title Fields --}}
                            <x-multilingual-input
                                name="title"
                                label="Title"
                                labelAr="العنوان"
                                placeholder="Enter occasion title"
                                placeholderAr="أدخل عنوان المناسبة"
                                :languages="$languages"
                                :model="$occasion ?? null"
                            />

                            {{-- Occasion Sub Title Fields --}}
                            <x-multilingual-input
                                name="sub_title"
                                label="Sub Title"
                                labelAr="العنوان الفرعي"
                                placeholder="Enter occasion sub title"
                                placeholderAr="أدخل العنوان الفرعي للمناسبة"
                                :languages="$languages"
                                :model="$occasion ?? null"
                            />

                            <div class="row">
                                {{-- Occasion Image --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <x-image-upload
                                            id="occasion_image"
                                            name="image"
                                            :placeholder="trans('catalogmanagement::occasion.image')"
                                            recommendedSize="Recommended size: 1538×402px"
                                            :existingImage="isset($occasion) && $occasion->image ? asset('storage/' . $occasion->image) : null"
                                            aspectRatio="16:9"
                                            :required="!isset($occasion)"
                                        />
                                        @error('image')
                                            <div class="invalid-feedback d-block" style="display: block !important;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                            </div>

                            {{-- Date Fields --}}
                            <div class="row">
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="start_date" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::occasion.start_date') }}
                                        </label>
                                        <input type="date"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               id="start_date"
                                               name="start_date"
                                               value="{{ old('start_date', isset($occasion) ? $occasion->start_date?->format('Y-m-d') : '') }}">
                                        @error('start_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="end_date" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::occasion.end_date') }}
                                        </label>
                                        <input type="date"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               id="end_date"
                                               name="end_date"
                                               value="{{ old('end_date', isset($occasion) ? $occasion->end_date?->format('Y-m-d') : '') }}">
                                        @error('end_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- SEO Information Section --}}
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="p-0 fw-500 fw-bold">{{ trans('catalogmanagement::occasion.seo_information') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- SEO Title Fields --}}
                                        <x-multilingual-input
                                            name="seo_title"
                                            label="SEO Title"
                                            labelAr="عنوان SEO"
                                            placeholder="Enter SEO title"
                                            placeholderAr="أدخل عنوان SEO"
                                            :languages="$languages"
                                            :model="$occasion ?? null"
                                        />

                                        {{-- SEO Description Fields --}}
                                        <x-multilingual-input
                                            name="seo_description"
                                            label="SEO Description"
                                            labelAr="وصف SEO"
                                            placeholder="Enter SEO description"
                                            placeholderAr="أدخل وصف SEO"
                                            type="textarea"
                                            rows="3"
                                            :languages="$languages"
                                            :model="$occasion ?? null"
                                        />

                                        {{-- SEO Keywords Fields --}}
                                        <x-multilingual-input
                                            name="seo_keywords"
                                            label="SEO Keywords"
                                            labelAr="كلمات مفتاحية SEO"
                                            placeholder="Type a keyword and press Enter..."
                                            placeholderAr="اكتب كلمة مفتاحية واضغط انتر"
                                            :tags="true"
                                            :languages="$languages"
                                            :model="$occasion ?? null"
                                        />
                                    </div>
                                </div>
                            </div>


                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="{{ route('admin.occasions.index') }}"
                                   class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> {{ trans('common.cancel') }}
                                </a>
                                <button type="submit" id="submitBtn"
                                        class="btn btn-primary btn-default btn-squared text-capitalize"
                                        style="display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="uil uil-check"></i>
                                    <span>{{ isset($occasion) ? trans('common.update') : trans('common.save') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
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
        // Initialize Select2 for vendor dropdown
        $('#vendor_id').select2({
            theme: 'bootstrap-5',
            placeholder: '{{ trans("common.select") }}',
            allowClear: true,
            width: '100%'
        });

        // Show/hide variants section based on vendor selection
        $('#vendor_id').on('change', function() {
            const vendorId = $(this).val();
            if (vendorId) {
                $('#variantsSection').slideDown();
                // Clear previous selections only when vendor changes
                $('#product_search').val('');
                $('#products-list').empty();
                $('#no-products').hide();
                selectedProducts = []; // Clear selections when vendor changes
            } else {
                $('#variantsSection').slideUp();
                $('#product_search').val('');
                $('#products-list').empty();
                selectedProducts = [];
            }
        });

        document.addEventListener('DOMContentLoaded', function() {

            let searchTimeout;
            let selectedProducts = [];

            // Product search functionality
            $('#product_search').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val().trim();
                const vendorId = $('#vendor_id').val();

                if (!vendorId) {
                    return;
                }

                if (searchTerm.length < 2) {
                    $('#products-list').empty();
                    $('#no-products').hide();
                    return;
                }

                searchTimeout = setTimeout(function() {
                    searchProducts(searchTerm, vendorId);
                }, 500);
            });

            // Search products via AJAX
            function searchProducts(search, vendorId) {
                $('#products-loading').show();
                $('#products-list').empty();
                $('#no-products').hide();

                $.ajax({
                    url: '/api/products',
                    method: 'GET',
                    headers: {
                        'lang' : "{{ app()->getLocale() }}"
                    },
                    data: {
                        search: search,
                        vendor_id: vendorId
                    },
                    success: function(response) {
                        console.log('response:', response);
                        $('#products-loading').hide();

                        if (response.status && response.data && response.data.length > 0) {
                            renderProducts(response.data);
                        } else {
                            $('#no-products').show();
                        }
                    },
                    error: function() {
                        $('#products-loading').hide();
                        $('#no-products').show();
                    }
                });
            }

            // Render product cards
            function renderProducts(products) {
                const container = $('#products-list');
                container.empty();

                // Store product details for the alert
                storeProductDetails(products);

                products.forEach(function(vendorProduct) {
                    // Check if this product is in the selected products array
                    const isSelected = selectedProducts.includes(parseInt(vendorProduct.id));

                    // Get product names in different languages
                    const name = vendorProduct.name || 'N/A';
                    const sku = vendorProduct.sku || 'N/A';

                    const card = `
                        <div class="product-card ${isSelected ? 'selected' : ''}" data-product-id="${vendorProduct.id}" style="cursor: pointer;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${name}</h6>
                                    <small class="text-muted">SKU: ${sku}</small>
                                </div>
                                ${isSelected ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="far fa-circle text-muted"></i>'}
                            </div>
                            <input type="hidden"
                                   name="product_variants[]"
                                   value="${vendorProduct.id}"
                                   class="product-variant-input"
                                   ${isSelected ? '' : 'disabled'}>
                        </div>
                    `;
                    container.append(card);
                });
            }

            // Handle product toggle - click entire card
            $(document).on('click', '.product-card', function() {
                const productId = parseInt($(this).data('product-id'));
                const hiddenInput = $(this).find('.product-variant-input');
                const isCurrentlySelected = selectedProducts.includes(productId);

                if (isCurrentlySelected) {
                    // Remove from selected products
                    selectedProducts = selectedProducts.filter(id => id !== productId);
                    $(this).removeClass('selected');
                    $(this).find('i').removeClass('fas fa-check-circle text-success').addClass('far fa-circle text-muted');
                    hiddenInput.prop('disabled', true);
                } else {
                    // Add to selected products
                    selectedProducts.push(productId);
                    $(this).addClass('selected');
                    $(this).find('i').removeClass('far fa-circle text-muted').addClass('fas fa-check-circle text-success');
                    hiddenInput.prop('disabled', false);
                }

                console.log('Selected products:', selectedProducts);
                updateSelectedProductsAlert();
            });

            // Store selected product details for the alert
            let selectedProductsDetails = {};

            // Update selected products alert
            function updateSelectedProductsAlert() {
                const count = selectedProducts.length;

                if (count > 0) {
                    $('#selected-products-alert').show();
                    $('#selected-count').text(count);

                    // Update the selected products list
                    let selectedHtml = '';
                    selectedProducts.forEach(function(productId) {
                        const product = selectedProductsDetails[productId];
                        if (product) {
                            selectedHtml += `
                                <span class="badge badge-primary badge-lg badge-round me-2 mb-1">
                                    ${product.name} (${product.sku})
                                    <button type="button" class="btn-close btn-close-white ms-1"
                                            data-product-id="${productId}"></button>
                                </span>
                            `;
                        }
                    });
                    $('#selected-products-list').html(selectedHtml);
                } else {
                    $('#selected-products-alert').hide();
                }
            }

            // Store product details when rendering
            function storeProductDetails(products) {
                products.forEach(function(product) {
                    selectedProductsDetails[product.id] = {
                        name: product.name || 'N/A',
                        sku: product.sku || 'N/A'
                    };
                });
            }

            // Clear all selections
            $(document).on('click', '#clear-selections', function() {
                selectedProducts = [];
                selectedProductsDetails = {};
                updateSelectedProductsAlert();

                // Update all product cards
                $('.product-card').removeClass('selected');
                $('.product-card i').removeClass('fas fa-check-circle text-success').addClass('far fa-circle text-muted');
                $('.product-variant-input').prop('disabled', true);
            });

            // Remove individual product from badge
            $(document).on('click', '#selected-products-list .btn-close', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const productId = parseInt($(this).data('product-id'));
                console.log('Removing product:', productId);

                // Remove from selected products
                selectedProducts = selectedProducts.filter(id => id !== productId);
                delete selectedProductsDetails[productId];

                // Update the card if it's visible
                $(`.product-card[data-product-id="${productId}"]`).removeClass('selected');
                $(`.product-card[data-product-id="${productId}"] i`).removeClass('fas fa-check-circle text-success').addClass('far fa-circle text-muted');
                $(`.product-card[data-product-id="${productId}"] .product-variant-input`).prop('disabled', true);

                updateSelectedProductsAlert();
                console.log('Updated selected products:', selectedProducts);
            });



            // Trigger change on page load if vendor is already selected
            if ($('#vendor_id').val()) {
                $('#variantsSection').show();
            }

            // AJAX Form Submission
            const occasionForm = document.getElementById('occasionForm');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alertContainer');

            // Clear validation errors on input
            occasionForm.querySelectorAll('input, textarea, select').forEach(input => {
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                        const feedback = this.parentNode.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.remove();
                        }
                    }
                });
            });

            occasionForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Disable submit button and show loading
                submitBtn.disabled = true;
                const btnIcon = submitBtn.querySelector('i');
                const btnText = submitBtn.querySelector('span:not(.spinner-border)');
                if (btnIcon) btnIcon.classList.add('d-none');
                if (btnText) btnText.classList.add('d-none');
                submitBtn.querySelector('.spinner-border').classList.remove('d-none');

                // Update loading text dynamically
                const loadingText = @json(isset($occasion) ? trans('loading.updating') : trans('loading.creating'));
                const loadingSubtext = '{{ trans("loading.please_wait") }}';
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.querySelector('.loading-text').textContent = loadingText;
                    overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
                }

                // Show loading overlay
                LoadingOverlay.show();

                // Clear previous alerts
                alertContainer.innerHTML = '';

                // Remove previous validation errors
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                // Start progress bar animation
                LoadingOverlay.animateProgressBar(30, 300).then(() => {
                    // Prepare form data
                    const formData = new FormData(occasionForm);

                    // Send AJAX request
                    return fetch(occasionForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                })
                .then(response => {
                    // Progress to 60%
                    LoadingOverlay.animateProgressBar(60, 200);

                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Progress to 90%
                    return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
                })
                .then(data => {
                    // Complete progress bar
                    return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                        // Show success animation with dynamic message
                        const successMessage = @json(isset($occasion) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                        LoadingOverlay.showSuccess(
                            successMessage,
                            '{{ trans("loading.redirecting") }}'
                        );

                        // Success - redirect after delay
                        if (data.success) {
                            setTimeout(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                }
                            }, 2000);
                        }
                    });
                })
                .catch(error => {
                    // Hide loading overlay and reset progress bar
                    LoadingOverlay.hide();

                    // Handle validation errors
                    if (error.errors) {
                        Object.keys(error.errors).forEach(field => {
                            // Convert Laravel dot notation to HTML bracket notation
                            let fieldName = field.replace(/\.(\d+)\./g, '[$1][').replace(/\.(\w+)$/, '[$1]');
                            if (fieldName.includes('[') && !fieldName.endsWith(']')) {
                                fieldName += ']';
                            }

                            // Try multiple selectors to find the input
                            let input = document.querySelector(`[name="${fieldName}"]`) ||
                                       document.querySelector(`[name="${field}"]`) ||
                                       document.querySelector(`input[name*="${field.split('.').pop()}"]`) ||
                                       document.querySelector(`textarea[name*="${field.split('.').pop()}"]`) ||
                                       document.querySelector(`select[name*="${field.split('.').pop()}"]`);

                            if (input) {
                                input.classList.add('is-invalid');

                                // Add invalid border to image upload container if it's an image field
                                if (field === 'image') {
                                    const imageContainer = input.closest('.dm-uploader');
                                    if (imageContainer) {
                                        imageContainer.style.border = '1px solid #dc3545';
                                        imageContainer.style.borderRadius = '4px';
                                    }
                                }

                                // Remove any existing error message for this field
                                const existingError = input.parentNode.querySelector('.invalid-feedback');
                                if (existingError) {
                                    existingError.remove();
                                }

                                // Get language information from the input's data-lang attribute or label
                                let languageName = '';
                                const langCode = input.getAttribute('data-lang');
                                if (langCode) {
                                    const label = input.parentNode.querySelector('label');
                                    if (label) {
                                        const labelText = label.textContent;
                                        const match = labelText.match(/\(([^)]+)\)/);
                                        if (match) {
                                            languageName = ` (${match[1]})`;
                                        }
                                    }
                                }

                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback d-block';
                                feedback.style.display = 'block !important';
                                feedback.textContent = error.errors[field][0] + languageName;
                                input.parentNode.appendChild(feedback);
                            }
                        });
                    }

                    // Show error message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show';
                    alert.innerHTML = `
                        ${error.message || 'An error occurred'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    alertContainer.appendChild(alert);

                    // Scroll to top to show errors
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                    // Re-enable submit button
                    submitBtn.disabled = false;
                    if (btnIcon) btnIcon.classList.remove('d-none');
                    if (btnText) btnText.classList.remove('d-none');
                    submitBtn.querySelector('.spinner-border').classList.add('d-none');
                });
            });
        });
    </script>
    @endpush
@endsection

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay
        :loadingText="trans('loading.processing')"
        :loadingSubtext="trans('loading.please_wait')"
    />
@endpush
