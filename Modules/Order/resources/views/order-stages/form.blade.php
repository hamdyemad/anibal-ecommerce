@extends('layout.app')
@section('title', (isset($orderStage)) ? trans('order::order_stage.edit_order_stage') : trans('order::order_stage.create_order_stage'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('order::order_stage.order_stages_management'), 'url' => route('admin.order-stages.index')],
                    ['title' => isset($orderStage) ? trans('order::order_stage.edit_order_stage') : trans('order::order_stage.create_order_stage')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($orderStage) ? trans('order::order_stage.edit_order_stage') : trans('order::order_stage.create_order_stage') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer" class="mb-2"></div>

                        <form id="orderStageForm"
                              action="{{ isset($orderStage) ? route('admin.order-stages.update', $orderStage->id) : route('admin.order-stages.store') }}"
                              method="POST">
                            @csrf
                            @if(isset($orderStage))
                                @method('PUT')
                            @endif

                            {{-- Order Stage Name Fields --}}
                            <x-multilingual-input
                                name="name"
                                label="Name"
                                labelAr="الاسم"
                                placeholder="Enter order stage name"
                                placeholderAr="أدخل اسم مرحلة الطلب"
                                :required="true"
                                :languages="$languages"
                                :model="$orderStage ?? null"
                            />

                            <div class="row">
                                {{-- Color Field --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order::order_stage.color') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="color"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               id="color"
                                               name="color"
                                               value="{{ old('color', $orderStage->color ?? '#3498db') }}"
                                               title="{{ trans('order::order_stage.choose_color') }}"
                                               style="width: 100%; height: 50px;">
                                        @error('color')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Sort Order Field --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order::order_stage.sort_order') }}
                                        </label>
                                        <input type="number"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               id="sort_order"
                                               name="sort_order"
                                               value="{{ old('sort_order', $orderStage->sort_order ?? 0) }}"
                                               min="0">
                                        @error('sort_order')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Activation Switcher --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order::order_stage.activation') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="active"
                                                       name="active"
                                                       value="1"
                                                       {{ old('active', $orderStage->active ?? 1) == 1 ? 'checked' : '' }}
                                                       @if(isset($orderStage) && $orderStage->is_system) disabled @endif>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- System Stage Info (Read-only) --}}
                                @if(isset($orderStage) && $orderStage->is_system)
                                <div class="col-md-6 mb-25">
                                    <div class="alert alert-warning mb-0">
                                        <i class="uil uil-shield-check me-1"></i>
                                        {{ trans('order::order_stage.system_stage') }}
                                    </div>
                                </div>
                                @endif
                            </div>

                            {{-- Form Actions --}}
                            <div class="row mt-30">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.order-stages.index') }}" class="btn btn-light btn-default btn-squared">
                                            <i class="uil uil-arrow-left me-1"></i>
                                            {{ trans('main.cancel') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-squared" id="submitBtn">
                                            <i class="uil uil-check me-1"></i>
                                            {{ isset($orderStage) ? trans('order::order_stage.update_order_stage') : trans('order::order_stage.create_order_stage') }}
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




    @push('scripts')
    <script>
        $(document).ready(function() {
            // Form submission with loading overlay
            $('#orderStageForm').on('submit', function(e) {
                e.preventDefault();

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '{{ isset($orderStage) ? trans('main.updating') : trans('main.creating') }}',
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
                                window.location.href = '{{ route('admin.order-stages.index') }}';
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
                            const message = xhr.responseJSON?.message || '{{ trans('order::order_stage.error_creating_order_stage') }}';
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
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        });
    </script>
    @endpush
@endsection

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay
        :loadingText="isset($orderStage) ? trans('main.updating') : trans('main.creating')"
        :loadingSubtext="trans('main.please wait')"
    />
@endpush
