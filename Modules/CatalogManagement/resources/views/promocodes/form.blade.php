@extends('layout.app')

@section('title', isset($promocode) ? __('catalogmanagement::promocodes.edit_promocode') : __('catalogmanagement::promocodes.create_promocode'))
@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('catalogmanagement::promocodes.title'), 'url' => route('admin.promocodes.index')],
                    ['title' => isset($promocode) ? __('catalogmanagement::promocodes.edit_promocode') : __('catalogmanagement::promocodes.create_promocode')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500 fw-bold">
                            {{ isset($promocode) ? __('catalogmanagement::promocodes.edit_promocode') : __('catalogmanagement::promocodes.create_promocode') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="alertContainer"></div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ __('catalogmanagement::promocodes.validation_errors') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="promocodeForm" method="POST" action="{{ isset($promocode) ? route('admin.promocodes.update', $promocode->id) : route('admin.promocodes.store') }}">
                            @csrf
                            @if(isset($promocode))
                                @method('PUT')
                            @endif
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="code" class="form-label">{{ __('catalogmanagement::promocodes.code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="code" name="code" value="{{ old('code', isset($promocode) ? $promocode->code : '') }}" placeholder="{{ __('catalogmanagement::promocodes.code') }}">
                                        @error('code')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="maximum_of_use" class="form-label">{{ __('catalogmanagement::promocodes.maximum_of_use') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="maximum_of_use" name="maximum_of_use" value="{{ old('maximum_of_use', isset($promocode) ? $promocode->maximum_of_use : 0) }}" min="0">
                                        @error('maximum_of_use')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="type" class="form-label">{{ __('catalogmanagement::promocodes.type') }} <span class="text-danger">*</span></label>
                                        <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="type" name="type">
                                            <option value="">{{ __('catalogmanagement::promocodes.type') }}</option>
                                            <option value="percent" {{ old('type', isset($promocode) ? $promocode->type : '') == 'percent' ? 'selected' : '' }}>{{ __('catalogmanagement::promocodes.types.percent') }}</option>
                                            <option value="amount" {{ old('type', isset($promocode) ? $promocode->type : '') == 'amount' ? 'selected' : '' }}>{{ __('catalogmanagement::promocodes.types.amount') }}</option>
                                        </select>
                                        @error('type')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="value" class="form-label">{{ __('catalogmanagement::promocodes.value') }} <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="value" name="value" value="{{ old('value', isset($promocode) ? $promocode->value : '') }}">
                                        @error('value')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="valid_from" class="form-label">{{ __('catalogmanagement::promocodes.valid_from') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="valid_from" name="valid_from" value="{{ old('valid_from', isset($promocode) ? $promocode->valid_from->format('Y-m-d') : '') }}">
                                        @error('valid_from')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="valid_until" class="form-label">{{ __('catalogmanagement::promocodes.valid_until') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="valid_until" name="valid_until" value="{{ old('valid_until', isset($promocode) ? $promocode->valid_until->format('Y-m-d') : '') }}">
                                        @error('valid_until')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="dedicated_to" class="form-label">{{ __('catalogmanagement::promocodes.dedicated_to') }} <span class="text-danger">*</span></label>
                                        <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="dedicated_to" name="dedicated_to">
                                            <option value="all" {{ old('dedicated_to', isset($promocode) ? $promocode->dedicated_to : '') == 'all' ? 'selected' : '' }}>{{ __('catalogmanagement::promocodes.dedicated_options.all') }}</option>
                                            <option value="male" {{ old('dedicated_to', isset($promocode) ? $promocode->dedicated_to : '') == 'male' ? 'selected' : '' }}>{{ __('catalogmanagement::promocodes.dedicated_options.male') }}</option>
                                            <option value="female" {{ old('dedicated_to', isset($promocode) ? $promocode->dedicated_to : '') == 'female' ? 'selected' : '' }}>{{ __('catalogmanagement::promocodes.dedicated_options.female') }}</option>
                                        </select>
                                        @error('dedicated_to')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ __('catalogmanagement::promocodes.activation') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="is_active"
                                                       name="is_active"
                                                       value="1"
                                                       {{ old('is_active', isset($promocode) ? $promocode->is_active : 1) == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active"></label>
                                            </div>
                                        </div>
                                        @error('is_active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mt-4 d-flex align-items-center justify-content-end">
                                        <a href="{{ route('admin.promocodes.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                            <i class="uil uil-arrow-left"></i> {{ __('catalogmanagement::promocodes.back_to_list') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize ms-2">
                                            <i class="uil uil-check"></i> {{ isset($promocode) ? __('catalogmanagement::promocodes.update') : __('catalogmanagement::promocodes.create') }}
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX Form Submission
    const form = document.getElementById('promocodeForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const alertContainer = document.getElementById('alertContainer');
    let originalBtnHtml = '';

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable submit button and show loading
        submitBtn.disabled = true;
        originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") ?? "Processing..." }}';

        // Update loading text dynamically
        const loadingText = @json(isset($promocode) ? __('catalogmanagement::promocodes.update') : __('catalogmanagement::promocodes.create'));
        const loadingSubtext = '{{ __('common.please_wait') ?? "Please wait..." }}';

        if (typeof LoadingOverlay !== 'undefined') {
             LoadingOverlay.show({
                text: loadingText,
                subtext: loadingSubtext
            });
        }

        // Clear previous alerts
        alertContainer.innerHTML = '';

        // Remove previous validation errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Start progress bar animation
        if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.animateProgressBar(30, 300);

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.animateProgressBar(60, 200);
            if (!response.ok) {
                return response.json().then(data => {
                    throw data;
                });
            }
            return response.json();
        })
        .then(data => {
            if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.animateProgressBar(100, 200);

            setTimeout(() => {
                 const successMessage = data.message || @json(isset($promocode) ? __('catalogmanagement::promocodes.messages.updated_successfully') : __('catalogmanagement::promocodes.messages.created_successfully'));

                 if (typeof LoadingOverlay !== 'undefined') {
                     LoadingOverlay.showSuccess(successMessage, '{{ __('catalogmanagement::promocodes.messages.redirecting') }}');
                 }

                 showAlert('success', successMessage);

                 setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("admin.promocodes.index") }}';
                 }, 1500);
            }, 500);
        })
        .catch(error => {
            if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();

            // Handle validation errors
            if (error.errors) {
                Object.keys(error.errors).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = error.errors[key][0];
                        input.parentNode.appendChild(feedback);

                        // Add listener to remove error on input change
                        input.addEventListener('input', function() {
                            this.classList.remove('is-invalid');
                            const feedbackEl = this.parentNode.querySelector('.invalid-feedback');
                            if (feedbackEl) feedbackEl.remove();
                        }, { once: true });
                    }
                });
                showAlert('danger', error.message || '{{ __('common.check_form_errors') ?? 'Please check the form for errors' }}');
            } else {
                showAlert('danger', error.message || '{{ __('common.error_occurred') ?? 'An error occurred' }}');
            }

            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        });
    });

    function showAlert(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show mb-20`;
        alert.innerHTML = `
            <i class="uil uil-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.getElementById('alertContainer').appendChild(alert);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>
@endpush

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay />
@endpush
