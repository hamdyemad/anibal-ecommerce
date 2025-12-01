@extends('layout.app')
@section('title', trans('order::order_stage.order_stage_details'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('order::order_stage.order_stages_management'), 'url' => route('admin.order-stages.index')],
                    ['title' => trans('order::order_stage.order_stage_details')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('order::order_stage.order_stage_details') }}</h5>
                        <div class="d-flex gap-2">
                            @can('order-stages.edit')
                                <a href="{{ route('admin.order-stages.edit', $orderStage->id) }}" class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-1"></i>
                                    {{ trans('main.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('admin.order-stages.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-1"></i>
                                {{ trans('main.back') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Basic Information --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3 fw-600 border-bottom pb-2">
                                    <i class="uil uil-info-circle me-1"></i>
                                    {{ trans('order::order_stage.basic_information') }}
                                </h6>
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- Name Translations --}}
                            @foreach($languages as $language)
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-2 d-block">
                                        {{ trans('order::order_stage.name') }} ({{ $language->name }})
                                    </label>
                                    <div class="p-3 bg-light rounded">
                                        {{ $orderStage->getTranslation('name', $language->code) ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="row mb-3">
                            {{-- Slug --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-2 d-block">
                                        {{ trans('main.slug') }}
                                    </label>
                                    <div class="p-3 bg-light rounded">
                                        <code>{{ $orderStage->slug }}</code>
                                    </div>
                                </div>
                            </div>

                            {{-- Color --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-2 d-block">
                                        {{ trans('order::order_stage.color') }}
                                    </label>
                                    <div class="p-3 bg-light rounded d-flex align-items-center">
                                        <span class="color-box me-3" style="width: 40px; height: 40px; background-color: {{ $orderStage->color }}; border-radius: 4px; border: 1px solid #ddd;"></span>
                                        <code>{{ $orderStage->color }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- Sort Order --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-2 d-block">
                                        {{ trans('order::order_stage.sort_order') }}
                                    </label>
                                    <div class="p-3 bg-light rounded">
                                        {{ $orderStage->sort_order }}
                                    </div>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-2 d-block">
                                        {{ trans('order::order_stage.status') }}
                                    </label>
                                    <div class="p-3 bg-light rounded">
                                        @if($orderStage->active)
                                            <span class="badge badge-success badge-round badge-lg">
                                                <i class="uil uil-check me-1"></i>
                                                {{ trans('order::order_stage.active') }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary badge-round badge-lg">
                                                <i class="uil uil-times me-1"></i>
                                                {{ trans('order::order_stage.inactive') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- System Stage --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-2 d-block">
                                        {{ trans('order::order_stage.system_stage') }}
                                    </label>
                                    <div class="p-3 bg-light rounded">
                                        @if($orderStage->is_system)
                                            <span class="badge badge-warning badge-round badge-lg">
                                                <i class="uil uil-shield-check me-1"></i>
                                                {{ trans('common.yes') }}
                                            </span>
                                        @else
                                            <span class="badge badge-light badge-round badge-lg">
                                                {{ trans('common.no') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Created At --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-2 d-block">
                                        {{ trans('main.created_at') }}
                                    </label>
                                    <div class="p-3 bg-light rounded">
                                        <i class="uil uil-calendar-alt me-1"></i>
                                        {{ $orderStage->created_at ? $orderStage->created_at->format('Y-m-d H:i:s') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- Updated At --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="il-gray fs-14 fw-500 mb-2 d-block">
                                        {{ trans('main.updated_at') }}
                                    </label>
                                    <div class="p-3 bg-light rounded">
                                        <i class="uil uil-calendar-alt me-1"></i>
                                        {{ $orderStage->updated_at ? $orderStage->updated_at->format('Y-m-d H:i:s') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    @can('order-stages.edit')
                                        <a href="{{ route('admin.order-stages.edit', $orderStage->id) }}" class="btn btn-primary btn-squared">
                                            <i class="uil uil-edit me-1"></i>
                                            {{ trans('main.edit') }}
                                        </a>
                                    @endcan
                                    @can('order-stages.delete')
                                        @if(!$orderStage->is_system)
                                            <button type="button" class="btn btn-danger btn-squared delete-order-stage-btn"
                                                    data-id="{{ $orderStage->id }}"
                                                    data-name="{{ $orderStage->getTranslation('name', app()->getLocale()) }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteOrderStageModal">
                                                <i class="uil uil-trash-alt me-1"></i>
                                                {{ trans('main.delete') }}
                                            </button>
                                        @endif
                                    @endcan
                                    <a href="{{ route('admin.order-stages.index') }}" class="btn btn-light btn-squared">
                                        <i class="uil uil-arrow-left me-1"></i>
                                        {{ trans('main.back') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal Component -->
    <x-delete-with-loading
        modalId="deleteOrderStageModal"
        tableId=""
        deleteButtonClass="delete-order-stage-btn"
        :title="trans('order::order_stage.delete_order_stage')"
        :message="trans('order::order_stage.delete_confirmation')"
        itemNameId="orderStageName"
        confirmBtnId="confirmDeleteOrderStage"
        :loadingText="trans('main.deleting')"
        :successMessage="trans('order::order_stage.order_stage_deleted')"
        :errorMessage="trans('order::order_stage.error_deleting_order_stage')"
    />

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Delete handler
            $(document).on('click', '.delete-order-stage-btn', function() {
                const orderStageId = $(this).data('id');
                const orderStageName = $(this).data('name');
                const deleteUrl = '{{ route('admin.order-stages.destroy', ':id') }}'.replace(':id', orderStageId);

                $('#orderStageName').text(orderStageName);
                $('#confirmDeleteOrderStage').data('url', deleteUrl);
            });

            // Handle successful deletion
            $(document).on('click', '#confirmDeleteOrderStage', function() {
                const deleteUrl = $(this).data('url');

                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#deleteOrderStageModal').modal('hide');
                            window.location.href = '{{ route('admin.order-stages.index') }}';
                        }
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
