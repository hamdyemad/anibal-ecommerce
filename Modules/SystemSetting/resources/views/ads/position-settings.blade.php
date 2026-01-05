@extends('layout.app')

@section('title', __('systemsetting::ads.position_settings'))

@section('content')
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => __('systemsetting::ads.ads_management'),
                        'url' => route('admin.system-settings.ads.index'),
                    ],
                    ['title' => __('systemsetting::ads.position_settings')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="uil uil-ruler me-2"></i>
                            {{ __('systemsetting::ads.position_settings') }}
                        </h6>
                        <a href="{{ route('admin.system-settings.ads.index') }}" class="btn btn-light btn-sm">
                            <i class="uil uil-arrow-left me-1"></i>
                            {{ __('systemsetting::ads.back_to_list') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <div id="alertContainer"></div>

                        <p class="text-muted mb-4">
                            <i class="uil uil-info-circle me-1"></i>
                            {{ __('systemsetting::ads.position_settings_description') }}
                        </p>

                        <form id="positionSettingsForm" method="POST" action="{{ route('admin.system-settings.ads.position-settings.update') }}">
                            @csrf
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 30%;">{{ __('systemsetting::ads.position') }}</th>
                                            <th style="width: 30%;">{{ __('systemsetting::ads.width') }} (px)</th>
                                            <th style="width: 30%;">{{ __('systemsetting::ads.height') }} (px)</th>
                                            <th style="width: 10%;">{{ __('systemsetting::ads.preview') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($positions as $key => $data)
                                            <tr>
                                                <td class="align-middle">
                                                    <strong>{{ $data['name'] }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $key }}</small>
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                        name="positions[{{ $key }}][width]" 
                                                        class="form-control ih-medium"
                                                        value="{{ $data['width'] }}"
                                                        min="0"
                                                        placeholder="Width">
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                        name="positions[{{ $key }}][height]" 
                                                        class="form-control ih-medium"
                                                        value="{{ $data['height'] }}"
                                                        min="0"
                                                        placeholder="Height">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <div class="dimension-preview" 
                                                        data-position="{{ $key }}"
                                                        style="display: inline-block; background: #e9ecef; border: 1px dashed #6c757d; max-width: 80px; max-height: 50px; overflow: hidden;">
                                                        <small class="preview-text text-muted" style="font-size: 10px;">
                                                            {{ $data['width'] }}x{{ $data['height'] }}
                                                        </small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary btn-default btn-squared">
                                    <i class="uil uil-check me-1"></i>
                                    {{ __('systemsetting::ads.save_settings') }}
                                </button>
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
    const form = document.getElementById('positionSettingsForm');
    const alertContainer = document.getElementById('alertContainer');

    // Update preview on input change
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            const widthInput = row.querySelector('input[name*="[width]"]');
            const heightInput = row.querySelector('input[name*="[height]"]');
            const previewText = row.querySelector('.preview-text');
            
            if (previewText && widthInput && heightInput) {
                previewText.textContent = `${widthInput.value || 0}x${heightInput.value || 0}`;
            }
        });
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> {{ __("common.processing") }}';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message || '{{ __("common.error_occurred") }}');
            }
        })
        .catch(error => {
            showAlert('danger', '{{ __("common.error_occurred") }}');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        });
    });

    function showAlert(type, message) {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="uil uil-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-1"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>
@endpush
