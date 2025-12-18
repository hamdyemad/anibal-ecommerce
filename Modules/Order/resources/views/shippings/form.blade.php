@extends('layout.app')
@section('title', isset($shipping) ? trans('shipping.edit_shipping') : trans('shipping.create_shipping'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('shipping.shipping_management'), 'url' => route('admin.shippings.index')],
                    ['title' => isset($shipping) ? trans('shipping.edit_shipping') : trans('shipping.create_shipping')],
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
                            @if (isset($shipping))
                                @method('PUT')
                            @endif

                            {{-- Dynamic Language Fields for Name --}}
                            <x-multilingual-input name="name" label="Name" labelAr="الاسم"
                                placeholder="Enter shipping name" placeholderAr="أدخل اسم الشحن" :required="true"
                                :languages="$languages" :model="$shipping ?? null" />

                            <div class="row">
                                {{-- Cost Field --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('shipping.cost') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="0.01"
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('cost') is-invalid @enderror"
                                            id="cost" name="cost" value="{{ old('cost', $shipping->cost ?? '') }}"
                                            placeholder="0.00" required>
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
                                                <input type="checkbox" class="form-check-input" id="active"
                                                    name="active" value="1"
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
                                {{-- Cities Field (Tag Input) --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('shipping.cities') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="tag-input-container" id="city-tags-container">
                                            <div class="tags-display" id="city-tags-display">
                                                @php
                                                    $selectedCityIds = old(
                                                        'city_ids',
                                                        isset($shipping)
                                                            ? $shipping->cities->pluck('id')->toArray()
                                                            : [],
                                                    );
                                                @endphp
                                                @foreach ($cities as $city)
                                                    @if (in_array($city->id, $selectedCityIds))
                                                        <span class="tag-badge" data-id="{{ $city->id }}">
                                                            {{ $city->name }}
                                                            <span class="tag-remove"
                                                                onclick="removeTag('city', {{ $city->id }})">&times;</span>
                                                            <input type="hidden" name="city_ids[]"
                                                                value="{{ $city->id }}">
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <input type="text" class="tag-input" id="city-input"
                                                placeholder="{{ trans('shipping.select_cities') }}" autocomplete="off">
                                            <div class="tag-dropdown" id="city-dropdown" style="display: none;">
                                                @foreach ($cities as $city)
                                                    <div class="tag-option" data-id="{{ $city->id }}"
                                                        data-name="{{ addslashes($city->name) }}"
                                                        onclick="addTag('city', {{ $city->id }}, '{{ addslashes($city->name) }}')">
                                                        {{ $city->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('city_ids')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Categories Field (Tag Input) --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('shipping.categories') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="tag-input-container" id="category-tags-container">
                                            <div class="tags-display" id="category-tags-display">
                                                @php
                                                    $selectedCategoryIds = old(
                                                        'category_ids',
                                                        isset($shipping)
                                                            ? $shipping->categories->pluck('id')->toArray()
                                                            : [],
                                                    );
                                                @endphp
                                                @foreach ($categories as $category)
                                                    @if (in_array($category->id, $selectedCategoryIds))
                                                        <span class="tag-badge" data-id="{{ $category->id }}">
                                                            {{ $category->name }}
                                                            <span class="tag-remove"
                                                                onclick="removeTag('category', {{ $category->id }})">&times;</span>
                                                            <input type="hidden" name="category_ids[]"
                                                                value="{{ $category->id }}">
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <input type="text" class="tag-input" id="category-input"
                                                placeholder="{{ trans('shipping.select_categories') }}" autocomplete="off">
                                            <div class="tag-dropdown" id="category-dropdown" style="display: none;">
                                                @foreach ($categories as $category)
                                                    <div class="tag-option" data-id="{{ $category->id }}"
                                                        data-name="{{ addslashes($category->name) }}"
                                                        onclick="addTag('category', {{ $category->id }}, '{{ addslashes($category->name) }}')">
                                                        {{ $category->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('category_ids')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Form Actions --}}
                            <div class="row mt-30">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.shippings.index') }}"
                                            class="btn btn-light btn-default btn-squared">
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

    @push('styles')
        <style>
            .tag-input-container {
                position: relative;
                border: 1px solid #e3e6ef;
                border-radius: 4px;
                padding: 8px;
                min-height: 45px;
                background: #fff;
                cursor: text;
            }

            .tag-input-container:focus-within {
                border-color: #0056B7;
                box-shadow: 0 0 0 0.2rem rgba(0, 86, 183, 0.15);
            }

            .tags-display {
                display: inline-flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-bottom: 4px;
            }

            .tag-badge {
                display: inline-flex;
                align-items: center;
                background-color: #0056B7;
                color: #fff;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 13px;
                font-weight: 500;
                gap: 6px;
            }

            .tag-remove {
                cursor: pointer;
                font-size: 18px;
                line-height: 1;
                font-weight: bold;
                opacity: 0.8;
                transition: opacity 0.2s;
            }

            .tag-remove:hover {
                opacity: 1;
                color: #ff6b6b;
            }

            .tag-input {
                border: none;
                outline: none;
                padding: 4px;
                font-size: 14px;
                flex: 1;
                min-width: 150px;
            }

            .tag-dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #fff;
                border: 1px solid #e3e6ef;
                border-radius: 4px;
                margin-top: 4px;
                max-height: 200px;
                overflow-y: auto;
                z-index: 1000;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .tag-option {
                padding: 10px 12px;
                cursor: pointer;
                transition: background-color 0.2s;
            }

            .tag-option:hover {
                background-color: #f8f9fa;
            }

            .tag-option.selected {
                background-color: #e3f2fd;
                color: #0056B7;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Wait for both DOM and jQuery to be ready
            (function() {
                function initTagInputs() {
                    if (typeof jQuery === 'undefined') {
                        console.error('jQuery is not loaded yet, retrying...');
                        setTimeout(initTagInputs, 100);
                        return;
                    }

                    console.log('Tag input initialization started');

                    // Tag input functionality
                    function setupTagInput(type) {
                        const input = $(`#${type}-input`);
                        const dropdown = $(`#${type}-dropdown`);
                        const container = $(`#${type}-tags-container`);

                        console.log(`Setting up ${type} tag input`, {
                            input: input.length,
                            dropdown: dropdown.length,
                            container: container.length
                        });

                        // Show dropdown on container click
                        container.on('click', function(e) {
                            e.stopPropagation();
                            input.focus();
                        });

                        // Show dropdown on focus
                        input.on('focus', function() {
                            console.log(`${type} input focused`);
                            dropdown.show();
                            filterOptions(type, '');
                        });

                        // Filter options on input
                        input.on('input', function() {
                            const searchTerm = $(this).val().toLowerCase();
                            filterOptions(type, searchTerm);
                        });

                        // Hide dropdown when clicking outside
                        $(document).on('click', function(e) {
                            if (!container.is(e.target) && container.has(e.target).length === 0) {
                                dropdown.hide();
                            }
                        });
                    }

                    function filterOptions(type, searchTerm) {
                        const dropdown = $(`#${type}-dropdown`);
                        dropdown.find('.tag-option').each(function() {
                            const optionText = $(this).data('name').toLowerCase();
                            const isSelected = $(
                                `#${type}-tags-display .tag-badge[data-id="${$(this).data('id')}"]`).length > 0;

                            if (isSelected) {
                                $(this).addClass('selected').hide();
                            } else if (searchTerm === '' || optionText.includes(searchTerm)) {
                                $(this).removeClass('selected').show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }

                    // Initialize both inputs
                    setupTagInput('city');
                    setupTagInput('category');
                    console.log('Tag input initialization completed');
                }

                // Start initialization when DOM is ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initTagInputs);
                } else {
                    initTagInputs();
                }
            })();

            // Add tag function
            function addTag(type, id, name) {
                console.log('addTag called:', {
                    type,
                    id,
                    name
                });
                const display = $(`#${type}-tags-display`);
                const input = $(`#${type}-input`);

                // Check if already added
                if (display.find(`.tag-badge[data-id="${id}"]`).length > 0) {
                    console.log('Tag already exists, skipping');
                    return;
                }

                // Create tag badge
                const tagHtml = `
                <span class="tag-badge" data-id="${id}">
                    ${name}
                    <span class="tag-remove" onclick="removeTag('${type}', ${id})">&times;</span>
                    <input type="hidden" name="${type}_ids[]" value="${id}">
                </span>
            `;

                display.append(tagHtml);
                input.val('').focus();

                // Update dropdown
                $(`#${type}-dropdown .tag-option[data-id="${id}"]`).addClass('selected').hide();
                console.log('Tag added successfully');
            }

            // Remove tag function
            function removeTag(type, id) {
                console.log('removeTag called:', {
                    type,
                    id
                });
                $(`#${type}-tags-display .tag-badge[data-id="${id}"]`).remove();
                $(`#${type}-dropdown .tag-option[data-id="${id}"]`).removeClass('selected').show();
                console.log('Tag removed successfully');
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
                            const message = xhr.responseJSON?.message ||
                                '{{ trans('shipping.error_creating') }}';
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
                $('html, body').animate({
                    scrollTop: 0
                }, 'slow');
            }
        </script>
    @endpush
@endsection

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay :loadingText="isset($shipping) ? trans('main.updating') : trans('main.creating')" :loadingSubtext="trans('main.please wait')" />
@endpush