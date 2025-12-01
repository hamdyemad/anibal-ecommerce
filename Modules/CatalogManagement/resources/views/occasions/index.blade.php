@extends('layout.app')
@section('title')
    {{ trans('catalogmanagement::occasion.occasions_management') }} | Bnaia
@endsection
@push('styles')
    <!-- Select2 CSS loaded via Vite -->
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('catalogmanagement::occasion.occasions_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('catalogmanagement::occasion.occasions_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.occasions.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('catalogmanagement::occasion.add_occasion') }}
                            </a>
                        </div>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    {{-- Search --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('common.search') }}..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ trans('catalogmanagement::occasion.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ trans('catalogmanagement::occasion.all_status') }}</option>
                                                <option value="1">{{ trans('catalogmanagement::occasion.active') }}</option>
                                                <option value="0">{{ trans('catalogmanagement::occasion.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('catalogmanagement::occasion.created_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_from_filter">
                                        </div>
                                    </div>

                                    {{-- Created Until --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_until_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('catalogmanagement::occasion.created_until') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_until_filter">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ __('common.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('common.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('common.reset_filters') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('common.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('common.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="occasionsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    @foreach($languages as $language)
                                        <th><span class="userDatatable-title">{{ trans('catalogmanagement::occasion.name') }} ({{ $language->name }})</span></th>
                                    @endforeach
                                    <th><span class="userDatatable-title">{{ trans('catalogmanagement::occasion.vendor') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('catalogmanagement::occasion.image') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('catalogmanagement::occasion.start_date') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('catalogmanagement::occasion.end_date') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('catalogmanagement::occasion.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('catalogmanagement::occasion.created_at') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <x-delete-with-loading modalId="modal-delete-occasion" tableId="occasionsDataTable" deleteButtonClass="delete-occasion"
        :title="trans('main.confirm delete')" :message="trans('main.are you sure you want to delete this')" itemNameId="delete-occasion-name" confirmBtnId="confirmDeleteOccasionBtn"
        :cancelText="trans('main.cancel')" :deleteText="trans('main.delete')" :loadingDeleting="trans('main.deleting')" :loadingPleaseWait="trans('main.please wait')" :loadingDeletedSuccessfully="trans('main.deleted success')" :loadingRefreshing="trans('main.refreshing')"
        :errorDeleting="trans('main.error on delete')" />

@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('created_from')) $('#created_from_filter').val(urlParams.get('created_from'));
            if (urlParams.has('created_until')) $('#created_until_filter').val(urlParams.get('created_until'));

            // Server-side processing with pagination
            let table = $('#occasionsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.occasions.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val();
                        d.created_from = $('#created_from_filter').val();
                        d.created_until = $('#created_until_filter').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    @foreach($languages as $language)
                    {
                        data: 'name.{{ $language->code }}',
                        name: 'translations.name',
                        orderable: false,
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    @endforeach
                    {
                        data: 'vendor',
                        name: 'vendor.name',
                        orderable: false,
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data) {
                                return `<img src="${data}" alt="Occasion Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">`;
                            }
                            return '<span class="text-muted">{{ trans("catalogmanagement::occasion.no_image") }}</span>';
                        }
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let checked = data ? 'checked' : '';
                            return `
                                <div class="custom-control custom-switch switch-primary switch-md">
                                    <input type="checkbox" class="custom-control-input status-switcher"
                                        id="switch-${row.id}"
                                        data-id="${row.id}"
                                        ${checked}>
                                    <label class="custom-control-label" for="switch-${row.id}"></label>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let showUrl = "{{ route('admin.occasions.show', ':id') }}".replace(':id', row.id);
                            let editUrl = "{{ route('admin.occasions.edit', ':id') }}".replace(':id', row.id);
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('catalogmanagement::occasion.view_occasion') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('catalogmanagement::occasion.edit_occasion') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    <a href="javascript:void(0);"
                                    class="remove delete-occasion btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-occasion"
                                    data-id="${row.id}"
                                    data-name="${row.name && row.name.en ? row.name.en : 'Occasion'}"
                                    data-url="{{ route('admin.occasions.index') }}/${row.id}"
                                    title="{{ trans('catalogmanagement::occasion.delete_occasion') }}">
                                        <i class="uil uil-trash-alt table_action_icon"></i>
                                    </a>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'desc']],
                pagingType: 'full_numbers',
                language: {
                    search: '',
                    searchPlaceholder: "{{ __('common.search') }}...",
                    lengthMenu: '_MENU_',
                    info: "{{ __('common.showing') }} _START_ {{ __('common.to') }} _END_ {{ __('common.of') }} _TOTAL_ {{ __('common.entries') }}",
                    infoEmpty: "{{ __('common.showing') }} 0 {{ __('common.to') }} 0 {{ __('common.of') }} 0 {{ __('common.entries') }}",
                    infoFiltered: "({{ __('common.filtered_from') }} _MAX_ {{ __('common.total_entries') }})",
                    zeroRecords: "{{ __('common.no_matching_records_found') }}",
                    emptyTable: "{{ __('common.no_data_available') }}",
                    paginate: {
                        first: '<i class="uil uil-angle-double-left"></i>',
                        last: '<i class="uil uil-angle-double-right"></i>',
                        next: '<i class="uil uil-angle-right"></i>',
                        previous: '<i class="uil uil-angle-left"></i>'
                    }
                },
                dom: '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-bordered');
                }
            });

            // Search button
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val('');
                $('#created_from_filter').val('');
                $('#created_until_filter').val('');
                table.ajax.reload();
            });

            // Entries per page
            $('#entriesSelect').on('change', function() {
                per_page = $(this).val();
                table.page.len(per_page).draw();
            });

            // Enter key to search
            $('#search').on('keypress', function(e) {
                if (e.which === 13) {
                    table.ajax.reload();
                }
            });

            // Status switcher
            $(document).on('change', '.status-switcher', function() {
                let switcher = $(this);
                let occasionId = switcher.data('id');
                let isActive = switcher.is(':checked') ? 1 : 0;

                // Show loading overlay
                LoadingOverlay.show();

                $.ajax({
                    url: '{{ route("admin.occasions.toggle-status", ":id") }}'.replace(':id', occasionId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        is_active: isActive
                    },
                    success: function(response) {
                        LoadingOverlay.hide();
                        if (response.status) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            switcher.prop('checked', !switcher.is(':checked'));
                        }
                    },
                    error: function(xhr) {
                        LoadingOverlay.hide();
                        switcher.prop('checked', !switcher.is(':checked'));
                        toastr.error('{{ trans("catalogmanagement::occasion.error_changing_status") }}');
                    }
                });
            });
        });
    </script>
@endpush
