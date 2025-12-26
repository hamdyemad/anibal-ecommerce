@extends('layout.app')

@section('title', __('accounting.expense_categories'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="crm mb-25">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="uil uil-estate"></i>{{ __('accounting.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.accounting.summary') }}">{{ __('accounting.accounting') }}</a></li>
                                <li class="breadcrumb-item active">{{ __('accounting.expense_items') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('accounting.expense_categories') }}</h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-default btn-squared text-capitalize" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="uil uil-plus"></i> {{ __('accounting.add_category') }}
                            </button>
                        </div>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    {{-- Search --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('accounting.search') }}
                                            </label>
                                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search" placeholder="{{ __('accounting.search_categories') }}..." autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status-filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i> {{ __('accounting.status') }}
                                            </label>
                                            <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="status-filter">
                                                <option value="">{{ __('accounting.all_status') }}</option>
                                                <option value="1">{{ __('accounting.active') }}</option>
                                                <option value="0">{{ __('accounting.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 d-flex align-items-center gap-2">
                                        <button type="button" id="searchBtn" class="btn btn-success btn-default btn-squared me-1">
                                            <i class="uil uil-search me-1"></i> {{ __('accounting.search') }}
                                        </button>
                                        <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared">
                                            <i class="uil uil-redo me-1"></i> {{ __('accounting.reset') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('accounting.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('accounting.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="expenseItemsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.created') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">{{ __('accounting.add_expense_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('accounting.close') }}"></button>
            </div>
            <form method="POST" action="{{ route('admin.accounting.expense-items.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name_en" class="form-label">{{ __('accounting.category_name') }} (English) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_en" name="name_en" required>
                    </div>
                    <div class="mb-3">
                        <label for="name_ar" class="form-label">{{ __('accounting.category_name') }} (العربية) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                    </div>
                    <div class="mb-3">
                        <label for="active" class="form-label">{{ __('accounting.status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                            <label class="form-check-label" for="active">{{ __('accounting.active') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('accounting.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('accounting.create_category') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Category Modal --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">{{ __('Edit Expense Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editCategoryForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name_en" class="form-label">{{ __('accounting.category_name') }} (English) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name_en" name="name_en" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_name_ar" class="form-label">{{ __('accounting.category_name') }} (العربية) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name_ar" name="name_ar" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_active" class="form-label">{{ __('accounting.status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_active" name="active" value="1">
                            <label class="form-check-label" for="edit_active">{{ __('accounting.active') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('accounting.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('accounting.update_category') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal with Loading Component --}}
<x-delete-with-loading modalId="modal-delete-expense-item" tableId="expenseItemsDataTable"
    deleteButtonClass="delete-expense-item" :title="__('accounting.confirm_delete')" :message="__('accounting.are_you_sure_delete')" itemNameId="delete-expense-item-name"
    confirmBtnId="confirmDeleteExpenseItemBtn" :cancelText="__('accounting.cancel')" :deleteText="__('accounting.delete')" :loadingDeleting="__('accounting.deleting')" :loadingPleaseWait="__('accounting.please_wait')"
    :loadingDeletedSuccessfully="__('accounting.category_deleted_successfully')" :loadingRefreshing="__('accounting.refreshing')" :errorDeleting="__('accounting.error_deleting_category')" />

<style>
    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: linear-gradient(90deg, #5f63f2 0%, #06d6a0 100%);
        border: none;
        padding: 1rem 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.6px;
        color: white;
    }

    .table tbody td {
        padding: 0.9rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
        font-size: 0.9rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
        background-color: #fff;
    }

    .table tbody tr:hover {
        background-color: #f8f9ff !important;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.05);
    }

    .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .table tbody tr:nth-child(even):hover {
        background-color: #f8f9ff !important;
    }

    .dataTables_wrapper {
        padding: 0;
    }

    .dataTables_info {
        padding: 1rem 25px;
        font-size: 0.875rem;
        color: #6c757d;
    }

    .dataTables_paginate {
        padding: 1rem 25px;
        text-align: right;
    }

    .dataTables_paginate .paginate_button {
        display: inline-block;
        padding: 0.5rem 0.8rem;
        margin: 0 0.25rem;
        border: 1px solid #dee2e6;
        background-color: #fff;
        color: #495057;
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1;
    }

    .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background-color: #5f63f2;
        border-color: #5f63f2;
        color: white;
    }

    .dataTables_paginate .paginate_button.active {
        background-color: #5f63f2 !important;
        border-color: #5f63f2 !important;
        color: white !important;
    }

    .dataTables_paginate .paginate_button.disabled,
    .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #f8f9fa;
        color: #6c757d;
        border-color: #dee2e6;
    }
</style>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            let per_page = 10;

            let table = $('#expenseItemsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.accounting.expense-items.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#status-filter').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'name', name: 'name' },
                    {
                        data: 'active',
                        name: 'active',
                        render: function(data) {
                            return data ?
                                '<span class="badge badge-lg badge-round bg-success">{{ __('accounting.active') }}</span>' :
                                '<span class="badge badge-lg badge-round bg-danger">{{ __('accounting.inactive') }}</span>';
                        }
                    },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[3, 'desc']],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                     '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function(settings) {
                    bindEditEvents();
                }
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#status-filter').on('change', function() {
                table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#status-filter').val('');
                table.ajax.reload();
            });

            function bindEditEvents() {
                $('.edit').off('click').on('click', function() {
                    const id = $(this).data('id');
                    const nameEn = $(this).data('name-en');
                    const nameAr = $(this).data('name-ar');
                    const active = $(this).data('active') === 1;

                    $('#edit_name_en').val(nameEn);
                    $('#edit_name_ar').val(nameAr);
                    $('#edit_active').prop('checked', active);

                    const form = $('#editCategoryForm');
                    const updateUrl = "{{ route('admin.accounting.expense-items.update', ':id') }}".replace(':id', id);
                    form.attr('action', updateUrl);
                });
            }
        });
    </script>
@endpush
