@extends('layout.app')

@section('title', __('systemsetting::return-policy.return_policy'))

@section('content')
<div class="container-fluid mb-3">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('systemsetting::return-policy.return_policy')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-default card-md mb-4">
                <div class="card-header">
                    <h6>{{ __('systemsetting::return-policy.return_policy') }}</h6>
                </div>
                <div class="card-body">
                    <div id="alertContainer"></div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="returnPolicyForm" method="POST" action="{{ route('admin.system-settings.return-policy.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Description Section --}}
                        <div class="card card-holder mb-4">
                            <div class="card-header">
                                <h3 class="fw-bold m-0">
                                    <i class="uil uil-file-text me-1"></i>{{ __('systemsetting::return-policy.description') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Description Multilingual --}}
                                    <div class="col-md-12">
                                        <x-multilingual-input
                                            name="description"
                                            oldPrefix="description"
                                            label="Description"
                                            :labelAr="'الوصف'"
                                            :placeholder="__('systemsetting::return-policy.description_placeholder')"
                                            :placeholderAr="__('systemsetting::return-policy.description_placeholder')"
                                            type="textarea"
                                            rows="6"
                                            :languages="$languages"
                                            :model="$returnPolicy ?? null"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="button-group d-flex pt-25 justify-content-end">
                            <a href="{{ route('admin.system-settings.return-policy.index') }}" class="btn btn-light btn-default btn-squared fw-400 text-capitalize me-2">
                                {{ __('systemsetting::return-policy.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize">
                                {{ __('systemsetting::return-policy.save') }}
                            </button>
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
    const returnPolicyForm = document.getElementById('returnPolicyForm');

    if (returnPolicyForm) {
        returnPolicyForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const formData = new FormData(this);

            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

            if (window.LoadingOverlay) {
                window.LoadingOverlay.show();
            }

            // Sync all CKEditor instances before submitting
            if (typeof CKEDITOR !== 'undefined') {
                for (let instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement();
                }
            }

            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.LoadingOverlay) {
                        window.LoadingOverlay.showSuccess(
                            data.message || '{{ __("systemsetting::return-policy.updated_successfully") }}',
                            'Redirecting...'
                        );
                    }

                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (window.LoadingOverlay) {
                        window.LoadingOverlay.hide();
                    }

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;

                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'An error occurred');
                    } else {
                        alert(data.message || 'An error occurred');
                    }

                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            let input = document.querySelector(`[name="${key}"]`);

                            if (input) {
                                input.classList.add('is-invalid');
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = data.errors[key][0];
                                input.parentNode.appendChild(errorDiv);
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);

                if (window.LoadingOverlay) {
                    window.LoadingOverlay.hide();
                }

                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred');
                } else {
                    alert('An error occurred');
                }
            });
        });

        document.querySelectorAll('input, select, textarea').forEach(input => {
            const clearError = () => {
                input.classList.remove('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            };

            input.addEventListener('input', clearError);
            input.addEventListener('change', clearError);
        });
    }
});
</script>
@endpush
