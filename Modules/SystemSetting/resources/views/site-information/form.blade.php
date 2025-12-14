@extends('layout.app')

@section('title', __('systemsetting::site-information.site_information'))

@section('content')
<div class="container-fluid mb-3">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('systemsetting::site-information.site_information'), 'url' => route('admin.system-settings.site-information.index')],
                ['title' => __('systemsetting::site-information.contact_us')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-default card-md mb-4">
                <div class="card-header">
                    <h6>{{ __('systemsetting::site-information.contact_us') }}</h6>
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

                    <form id="siteInformationForm" method="POST" action="{{ route('admin.system-settings.site-information.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Social Media Links Section --}}
                        <div class="card card-holder mb-4">
                            <div class="card-header">
                                <h3 class="fw-bold m-0">
                                    <i class="uil uil-share-alt me-1"></i>{{ __('systemsetting::site-information.social_media_links') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Facebook URL --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="facebook_url" class="form-label">
                                                <i class="uil uil-facebook-f me-1"></i>{{ __('systemsetting::site-information.facebook_url') }}
                                            </label>
                                            <input type="url" name="facebook_url" id="facebook_url" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('facebook_url') is-invalid @enderror"
                                                   placeholder="{{ __('systemsetting::site-information.facebook_url_placeholder') }}"
                                                   value="{{ old('facebook_url', $siteInformation->facebook_url ?? '') }}">
                                            @error('facebook_url')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- LinkedIn URL --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="linkedin_url" class="form-label">
                                                <i class="uil uil-linkedin-alt me-1"></i>{{ __('systemsetting::site-information.linkedin_url') }}
                                            </label>
                                            <input type="url" name="linkedin_url" id="linkedin_url" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('linkedin_url') is-invalid @enderror"
                                                   placeholder="{{ __('systemsetting::site-information.linkedin_url_placeholder') }}"
                                                   value="{{ old('linkedin_url', $siteInformation->linkedin_url ?? '') }}">
                                            @error('linkedin_url')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Twitter/X URL --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="twitter_url" class="form-label">
                                                <i class="uil uil-twitter-alt me-1"></i>{{ __('systemsetting::site-information.twitter_url') }}
                                            </label>
                                            <input type="url" name="twitter_url" id="twitter_url" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('twitter_url') is-invalid @enderror"
                                                   placeholder="{{ __('systemsetting::site-information.twitter_url_placeholder') }}"
                                                   value="{{ old('twitter_url', $siteInformation->twitter_url ?? '') }}">
                                            @error('twitter_url')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Instagram URL --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="instagram_url" class="form-label">
                                                <i class="uil uil-instagram me-1"></i>{{ __('systemsetting::site-information.instagram_url') }}
                                            </label>
                                            <input type="url" name="instagram_url" id="instagram_url" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('instagram_url') is-invalid @enderror"
                                                   placeholder="{{ __('systemsetting::site-information.instagram_url_placeholder') }}"
                                                   value="{{ old('instagram_url', $siteInformation->instagram_url ?? '') }}">
                                            @error('instagram_url')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Contact Information Section --}}
                        <div class="card card-holder mb-4">
                            <div class="card-header">
                                <h3 class="fw-bold m-0">
                                    <i class="uil uil-phone me-1"></i>{{ __('systemsetting::site-information.contact_information') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Phone 1 --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="phone_1" class="form-label">
                                                {{ __('systemsetting::site-information.phone_1') }}
                                            </label>
                                            <input type="tel" name="phone_1" id="phone_1" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('phone_1') is-invalid @enderror"
                                                   placeholder="{{ __('systemsetting::site-information.phone_placeholder') }}"
                                                   value="{{ old('phone_1', $siteInformation->phone_1 ?? '') }}">
                                            @error('phone_1')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Phone 2 --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="phone_2" class="form-label">
                                                {{ __('systemsetting::site-information.phone_2') }}
                                            </label>
                                            <input type="tel" name="phone_2" id="phone_2" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('phone_2') is-invalid @enderror"
                                                   placeholder="{{ __('systemsetting::site-information.phone_placeholder') }}"
                                                   value="{{ old('phone_2', $siteInformation->phone_2 ?? '') }}">
                                            @error('phone_2')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="email" class="form-label">
                                                <i class="uil uil-envelope me-1"></i>{{ __('systemsetting::site-information.email') }}
                                            </label>
                                            <input type="email" name="email" id="email" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('email') is-invalid @enderror"
                                                   placeholder="{{ __('systemsetting::site-information.email_placeholder') }}"
                                                   value="{{ old('email', $siteInformation->email ?? '') }}">
                                            @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Google Maps URL --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="google_maps_url" class="form-label">
                                                <i class="uil uil-map-pin me-1"></i>{{ __('systemsetting::site-information.google_maps_url') }}
                                            </label>
                                            <input type="url" name="google_maps_url" id="google_maps_url" class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('google_maps_url') is-invalid @enderror"
                                                   placeholder="{{ __('systemsetting::site-information.google_maps_placeholder') }}"
                                                   value="{{ old('google_maps_url', $siteInformation->google_maps_url ?? '') }}">
                                            @error('google_maps_url')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Address --}}
                                        <x-multilingual-input
                                            name="address"
                                            oldPrefix="address"
                                            label="Address"
                                            :labelAr="'العنوان'"
                                            :placeholder="'Address'"
                                            :placeholderAr="'العنوان'"
                                            type="textarea"
                                            rows="4"
                                            :languages="$languages"
                                            :model="$siteInformation ?? null"
                                            inputClass="nockeditor"
                                        />
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="button-group d-flex pt-25 justify-content-end">
                            <a href="{{ route('admin.system-settings.site-information.index') }}" class="btn btn-light btn-default btn-squared fw-400 text-capitalize me-2">
                                {{ __('systemsetting::site-information.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize">
                                {{ __('systemsetting::site-information.save') }}
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
    const siteInfoForm = document.getElementById('siteInformationForm');

    if (siteInfoForm) {
        siteInfoForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const formData = new FormData(this);

            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

            if (window.LoadingOverlay) {
                window.LoadingOverlay.show();
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
                            data.message || '{{ __("systemsetting::site-information.updated_successfully") }}',
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
