@extends('layout.app')
@section('title', trans('systemsetting::points.points_system_settings'))

@push('styles')
    <style>
        .points-table-container {
            border-radius: 8px;
            overflow: hidden;
        }

        .points-input {
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            padding: 8px 10px;
            font-size: 13px;
            transition: all 0.3s ease;
            width: 100%;
            height: auto;
        }

        .points-input:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.2rem rgba(var(--color-primary-rgb), 0.15);
            outline: none;
        }

        .points-input.saving {
            opacity: 0.6;
            pointer-events: none;
        }

        .currency-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #f0f0f0;
            border-radius: 6px;
            font-weight: 500;
        }

        .alert {
            border: none;
            border-radius: 8px;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .form-switch-primary .form-check-input:checked {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
        }

        .error-message {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            font-weight: 500;
            color: #dc3545;
        }

        .points-input.is-invalid {
            border-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }

        .points-input.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
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
                        'title' => trans('systemsetting::points.points_system_settings'),
                    ],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500 fw-bold">
                            {{ trans('systemsetting::points.points_system_settings') }}
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

                        <!-- Points System Enable/Disable Switcher -->
                        <div class="row mb-30">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                        {{ trans('systemsetting::points.is_enabled') }}
                                    </label>
                                    <div class="dm-switch-wrap d-flex align-items-center">
                                        <div class="form-check form-switch form-switch-primary form-switch-md">
                                            <input type="hidden" name="points_system_enabled" value="0" id="points_system_enabled_hidden">
                                            <input type="checkbox" class="form-check-input" id="points_system_enabled"
                                                name="points_system_enabled" value="1"
                                                @if($pointSystem->is_enabled)
                                                    checked
                                                @endif
                                                onchange="togglePointsSystem(this)">
                                            <label class="form-check-label ms-2" for="points_system_enabled">
                                                <span id="enabledText">{{ trans('common.disabled') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">{{ trans('systemsetting::points.is_enabled_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-30">

                        <!-- Currencies Points Settings Table -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-20 fw-500">{{ trans('systemsetting::points.currency_settings') }}</h6>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive points-table-container">
                                    <table class="table mb-0 table-bordered table-hover">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th class="text-center"><span class="userDatatable-title">#</span></th>
                                                <th><span class="userDatatable-title">{{ trans('systemsetting::points.currency_name') }}</span></th>
                                                <th><span class="userDatatable-title">{{ trans('systemsetting::points.points_per_currency') }}</span></th>
                                                <th><span class="userDatatable-title">{{ trans('systemsetting::points.welcome_bonus') }}</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($currencies as $index => $currency)
                                            <tr class="userDatatable-row">
                                                <td class="text-center">
                                                    <span class="userDatatable-content">{{ $index + 1 }}</span>
                                                </td>
                                                <td>
                                                    <div class="userDatatable-content text-center">
                                                        <div style="display: flex; gap: 8px; align-items: center;">
                                                            <span class="fw-semibold text-dark m-0">
                                                                {{ $currency->name }}
                                                                @if($currency->symbol)
                                                                    {{ ' (' . $currency->symbol . ')' }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="userDatatable-content">
                                                        <input type="number"
                                                               class="form-control form-control-sm points-input"
                                                               step="0.01"
                                                               min="0"
                                                               max="999999.99"
                                                               data-currency-id="{{ $currency->id }}"
                                                               data-type="points_value"
                                                               placeholder="0.00"
                                                               value="{{ $currency->pointSetting?->points_value ?? 0 }}">
                                                        <small class="text-danger error-message" style="display: none;"></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="userDatatable-content">
                                                        <input type="number"
                                                               class="form-control form-control-sm points-input"
                                                               step="0.01"
                                                               min="0"
                                                               max="999999.99"
                                                               data-currency-id="{{ $currency->id }}"
                                                               data-type="welcome_points"
                                                               placeholder="0.00"
                                                               value="{{ $currency->pointSetting?->welcome_points ?? 0 }}">
                                                        <small class="text-danger error-message" style="display: none;"></small>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-40">
                                                    <i class="uil uil-inbox fs-1 mb-2"></i>
                                                    <p class="mt-2">{{ trans('common.no_data_available') }}</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let saveTimeout;

        function togglePointsSystem(checkbox) {
            const enabledText = document.getElementById('enabledText');

            // Show loading state
            checkbox.disabled = true;

            // Prepare form data
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');

            // Make AJAX request to toggle the points system
            fetch('{{ route("admin.points-system.toggle-enabled") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                checkbox.disabled = false;

                if (data.success) {
                    // Update UI based on response
                    if (data.is_enabled) {
                        enabledText.textContent = '{{ trans("common.enabled") }}';
                        document.getElementById('points_system_enabled_hidden').value = '1';
                        checkbox.checked = true;
                    } else {
                        enabledText.textContent = '{{ trans("common.disabled") }}';
                        document.getElementById('points_system_enabled_hidden').value = '0';
                        checkbox.checked = false;
                    }

                    // Show success message
                    toastr.success(data.message || '{{ trans("systemsetting::points.points_system_updated") }}');
                } else {
                    // Revert checkbox on error
                    checkbox.checked = !checkbox.checked;
                    toastr.error(data.message || '{{ trans("systemsetting::points.error_updating_setting") }}');
                }
            })
            .catch(error => {
                checkbox.disabled = false;
                checkbox.checked = !checkbox.checked;
                console.error('Error:', error);
                toastr.error('{{ trans("systemsetting::points.error_updating_setting") }}');
            });
        }

        function savePointsSetting(input) {
            const currencyId = input.dataset.currencyId;
            const type = input.dataset.type;
            const value = input.value;

            // Clear previous timeout
            clearTimeout(saveTimeout);

            // Add saving state
            input.classList.add('saving');

            // Debounce the save request
            saveTimeout = setTimeout(() => {
                const formData = new FormData();
                formData.append('currency_id', currencyId);
                formData.append(type, value);
                formData.append('is_active', 1);
                formData.append('_token', '{{ csrf_token() }}');

                // Check if setting exists
                const existingSetting = document.querySelector(`input[data-currency-id="${currencyId}"][data-type="points_value"]`).closest('tr');
                const settingId = existingSetting?.dataset.settingId;

                const url = settingId
                    ? '{{ route("admin.points-settings.update", ":id") }}'.replace(':id', settingId)
                    : '{{ route("admin.points-settings.store") }}';

                const method = settingId ? 'PUT' : 'POST';
                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    input.classList.remove('saving');

                    // Clear all error messages first
                    document.querySelectorAll('.error-message').forEach(el => {
                        el.style.display = 'none';
                        el.textContent = '';
                    });
                    document.querySelectorAll('.points-input').forEach(el => {
                        el.classList.remove('is-invalid');
                    });

                    if (data.success) {
                        toastr.success(data.message || '{{ trans("systemsetting::points.points_setting_updated") }}');

                        // Update the row with the setting ID if it's a new creation
                        if (!settingId && data.data?.id) {
                            existingSetting.dataset.settingId = data.data.id;
                        }
                    } else {
                        // Handle validation errors
                        if (data.errors && typeof data.errors === 'object') {
                            Object.keys(data.errors).forEach(fieldName => {
                                const errors = data.errors[fieldName];
                                const errorMessage = Array.isArray(errors) ? errors[0] : errors;

                                // Find the input field for this currency and type
                                const fieldInput = document.querySelector(
                                    `input[data-currency-id="${currencyId}"][data-type="${fieldName}"]`
                                );

                                if (fieldInput) {
                                    fieldInput.classList.add('is-invalid');
                                    const errorEl = fieldInput.closest('.userDatatable-content').querySelector('.error-message');
                                    if (errorEl) {
                                        errorEl.textContent = errorMessage;
                                        errorEl.style.display = 'block';
                                    }
                                }
                            });
                            toastr.error('{{ trans("systemsetting::points.error_updating_setting") }}');
                        } else {
                            toastr.error(data.message || '{{ trans("systemsetting::points.error_updating_setting") }}');
                        }
                    }
                })
                .catch(error => {
                    input.classList.remove('saving');
                    console.error('Error:', error);
                    toastr.error('{{ trans("systemsetting::points.error_updating_setting") }}');
                });
            }, 1000); // Wait 1 second after user stops typing
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('points_system_enabled');
            if (checkbox && checkbox.checked) {
                document.getElementById('enabledText').textContent = '{{ trans("common.enabled") }}';
            }

            // Add event listeners to points inputs
            document.querySelectorAll('.points-input').forEach(input => {
                input.addEventListener('change', function() {
                    savePointsSetting(this);
                });

                input.addEventListener('blur', function() {
                    savePointsSetting(this);
                });
            });

        });
    </script>
@endsection
