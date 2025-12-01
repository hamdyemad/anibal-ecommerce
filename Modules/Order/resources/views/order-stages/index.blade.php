@extends('layout.app')
@section('title', trans('order::order_stage.order_stages_management'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('order::order_stage.order_stages_management')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('order::order_stage.order_stages_list') }}</h5>
                        @can('order-stages.create')
                            <a href="{{ route('admin.order-stages.create') }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-plus me-1"></i>
                                {{ trans('order::order_stage.add_order_stage') }}
                            </a>
                        @endcan
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer" class="mb-2"></div>

                        <!-- Filters -->
                        <div class="row mb-20">
                            <div class="col-md-3 mb-2">
                                <input type="text" id="searchInput" class="form-control" placeholder="{{ trans('order::order_stage.search_order_stages') }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <select id="statusFilter" class="form-control">
                                    <option value="">{{ trans('order::order_stage.all_status') }}</option>
                                    <option value="1">{{ trans('order::order_stage.active') }}</option>
                                    <option value="0">{{ trans('order::order_stage.inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <input type="date" id="createdFromFilter" class="form-control" placeholder="{{ trans('order::order_stage.created_from') }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <input type="date" id="createdUntilFilter" class="form-control" placeholder="{{ trans('order::order_stage.created_until') }}">
                            </div>
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table id="orderStagesTable" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th><span class="userDatatable-title">#</span></th>
                                        <th><span class="userDatatable-title">{{ trans('order::order_stage.name') }}</span></th>
                                        <th><span class="userDatatable-title">{{ trans('main.slug') }}</span></th>
                                        <th><span class="userDatatable-title">{{ trans('order::order_stage.color') }}</span></th>
                                        <th><span class="userDatatable-title">{{ trans('order::order_stage.sort_order') }}</span></th>
                                        <th><span class="userDatatable-title">{{ trans('order::order_stage.status') }}</span></th>
                                        <th><span class="userDatatable-title">{{ trans('order::order_stage.system_stage') }}</span></th>
                                        <th><span class="userDatatable-title">{{ trans('main.created_at') }}</span></th>
                                        <th><span class="userDatatable-title">{{ trans('main.actions') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal Component -->
    <x-delete-with-loading
        modalId="deleteOrderStageModal"
        tableId="orderStagesTable"
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
            // Initialize DataTable
            const table = $('#orderStagesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.order-stages.datatable') }}',
                    data: function(d) {
                        d.search = $('#searchInput').val();
                        d.status = $('#statusFilter').val();
                        d.created_from = $('#createdFromFilter').val();
                        d.created_until = $('#createdUntilFilter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'slug', name: 'slug' },
                    {
                        data: 'color',
                        name: 'color',
                        render: function(data) {
                            return `<div class="d-flex align-items-center">
                                <span class="color-box me-2" style="width: 30px; height: 30px; background-color: ${data}; border-radius: 4px; border: 1px solid #ddd;"></span>
                                <span>${data}</span>
                            </div>`;
                        }
                    },
                    { data: 'sort_order', name: 'sort_order' },
                    {
                        data: 'active',
                        name: 'active',
                        render: function(data) {
                            return data
                                ? '<span class="badge badge-success badge-round badge-lg"><i class="uil uil-check me-1"></i>{{ trans('order::order_stage.active') }}</span>'
                                : '<span class="badge badge-secondary badge-round badge-lg"><i class="uil uil-times me-1"></i>{{ trans('order::order_stage.inactive') }}</span>';
                        }
                    },
                    {
                        data: 'is_system',
                        name: 'is_system',
                        render: function(data) {
                            return data
                                ? '<span class="badge badge-warning badge-round badge-lg"><i class="uil uil-shield-check me-1"></i>{{ trans('common.yes') }}</span>'
                                : '<span class="badge badge-light badge-round badge-lg">{{ trans('common.no') }}</span>';
                        }
                    },
                    { data: 'created_at', name: 'created_at' },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[4, 'asc']], // Sort by sort_order by default
                language: {
                    processing: '{{ trans('main.processing') }}',
                    search: '{{ trans('main.search') }}:',
                    lengthMenu: '{{ trans('main.show') }} _MENU_ {{ trans('main.entries') }}',
                    info: '{{ trans('main.showing') }} _START_ {{ trans('main.to') }} _END_ {{ trans('main.of') }} _TOTAL_ {{ trans('main.entries') }}',
                    infoEmpty: '{{ trans('main.showing') }} 0 {{ trans('main.to') }} 0 {{ trans('main.of') }} 0 {{ trans('main.entries') }}',
                    infoFiltered: '({{ trans('main.filtered_from') }} _MAX_ {{ trans('main.total_entries') }})',
                    paginate: {
                        first: '{{ trans('main.first') }}',
                        last: '{{ trans('main.last') }}',
                        next: '{{ trans('main.next') }}',
                        previous: '{{ trans('main.previous') }}'
                    },
                    emptyTable: '{{ trans('main.no_data_available') }}'
                }
            });

            // Filter handlers
            $('#searchInput, #statusFilter, #createdFromFilter, #createdUntilFilter').on('change keyup', function() {
                table.draw();
            });

            // Delete handler
            $(document).on('click', '.delete-order-stage-btn', function() {
                const orderStageId = $(this).data('id');
                const orderStageName = $(this).data('name');
                const deleteUrl = '{{ route('admin.order-stages.destroy', ':id') }}'.replace(':id', orderStageId);

                $('#orderStageName').text(orderStageName);
                $('#confirmDeleteOrderStage').data('url', deleteUrl);
            });

            // Status toggle handler
            $(document).on('click', '.toggle-status-btn', function(e) {
                e.preventDefault();
                const orderStageId = $(this).data('id');
                const currentStatus = $(this).data('status');

                $.ajax({
                    url: '{{ route('admin.order-stages.toggle-status', ':id') }}'.replace(':id', orderStageId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            table.ajax.reload(null, false);
                        } else {
                            showAlert('danger', response.message);
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || '{{ trans('order::order_stage.error_changing_status') }}';
                        showAlert('danger', message);
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
                setTimeout(() => {
                    $('.alert').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    </script>
    @endpush
@endsection
