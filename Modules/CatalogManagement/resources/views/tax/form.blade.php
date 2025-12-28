@extends('layout.app')
@section('title')
    {{ isset($tax) ? __('catalogmanagement::tax.edit_tax') : __('catalogmanagement::tax.create_tax') }} | Bnaia
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('catalogmanagement::tax.taxes_management'), 'url' => route('admin.taxes.index')],
                    ['title' => isset($tax) ? __('catalogmanagement::tax.edit_tax') : __('catalogmanagement::tax.create_tax')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">{{ isset($tax) ? __('catalogmanagement::tax.edit_tax') : __('catalogmanagement::tax.create_tax') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="alertContainer"></div>

                        <form id="taxForm" method="POST" action="{{ isset($tax) ? route('admin.taxes.update', $tax->id) : route('admin.taxes.store') }}">
                            @csrf
                            @if(isset($tax))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <x-multilingual-input
                                    name="name"
                                    oldPrefix="translations"
                                    label="Name"
                                    :labelAr="'الاسم'"
                                    :placeholder="'Tax name'"
                                    :placeholderAr="'اسم الضريبة'"
                                    type="text"
                                    :languages="$languages"
                                    :model="$tax ?? null"
                                />

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="percentage" class="form-label">{{ __('catalogmanagement::tax.percentage') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" max="100" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="percentage" name="percentage" value="{{ old('percentage', isset($tax) ? $tax->percentage : '') }}" placeholder="e.g., 14.00">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('percentage')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">{{ __('catalogmanagement::tax.status') }}</label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', isset($tax) ? $tax->is_active : 1) == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mt-4 d-flex align-items-center justify-content-end">
                                        <a href="{{ route('admin.taxes.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                            <i class="uil uil-arrow-left"></i> {{ __('catalogmanagement::tax.back_to_list') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize ms-2">
                                            <i class="uil uil-check"></i> {{ isset($tax) ? __('catalogmanagement::tax.update_tax') : __('catalogmanagement::tax.create_tax') }}
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
$(document).ready(function() {
    $('#taxForm').on('submit', function(e) {
        e.preventDefault();
        
        let form = $(this);
        let submitBtn = form.find('button[type="submit"]');
        let originalBtnHtml = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>{{ __("common.processing") }}');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    let errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value[0] + '</li>';
                    });
                    errorHtml += '</ul></div>';
                    $('#alertContainer').html(errorHtml);
                }
            }
        });
    });
});
</script>
@endpush
