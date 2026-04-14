@extends('layout.app')
@section('title')
    {{ trans('shipping.shipping_management') }} | Bnaia
@endsection

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
                    ['title' => trans('shipping.shipping_management')],
                ]" />
            </div>
        </div>

        {{-- Shipping Settings Card --}}
        <div class="row">
            <div class="col-lg-12 mb-25">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-500">
                            <i class="uil uil-setting me-2"></i>{{ trans('shipping.shipping_settings') }}
                        </h6>
                    </div>
                    <div class="card-body p-25">
                        <div class="alert alert-info d-flex align-items-start mb-0">
                            <i class="uil uil-info-circle me-3 fs-4"></i>
                            <div>
                                <strong class="d-block mb-2">{{ trans('shipping.city_based_shipping') }}</strong>
                                <p class="mb-0">{{ trans('shipping.city_based_shipping_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('shipping.shipping_management') }}</h4>
                        @can('shippings.create')
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.shippings.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('shipping.create_shipping') }}
                            </a>
                        </div>
                        @endcan
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
                                                {{ trans('shipping.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ trans('common.all') }}</option>
                                                <option value="1">{{ trans('shipping.active') }}</option>
                                                <option value="0">{{ trans('shipping.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('shipping.created_from') }}
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
                                                {{ trans('shipping.created_until') }}
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
                        <table id="shippingsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.cities') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.cost') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.created_at') }}</span></th>
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

    {{-- Delete Confirmation Modal --}}
    <x-delete-modal 
        modalId="modal-delete-shipping" 
        :title="trans('shipping.confirm_delete')" 
        :message="trans('shipping.delete_confirmation')" 
        itemNameId="delete-shipping-name"
        confirmBtnId="confirmDeleteShippingBtn" 
        deleteRoute="{{ rtrim(route('admin.shippings.index'), '/') }}"
        :cancelText="trans('main.cancel')" 
        :deleteText="trans('shipping.delete')" />


@endsection

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
            let table = $('#shippingsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.shippings.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val() !== '' ? $('#active').val() : null;
                        d.created_date_from = $('#created_from_filter').val();
                        d.created_date_to = $('#created_until_filter').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'cities',
                        name: 'cities',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (!data || data.length === 0) {
                                return '<span class="text-muted">-</span>';
                            }
                            
                            const cityNames = data.map(city => city.name);
                            
                            if (cityNames.length <= 2) {
                                return cityNames.join(', ');
                            }
                            
                            // Show first 2 cities and count of remaining
                            const displayCities = cityNames.slice(0, 2).join(', ');
                            const remaining = cityNames.length - 2;
                            return `${displayCities} <span class="badge badge-info">+${remaining}</span>`;
                        }
                    },
                    {
                        data: 'cost',
                        name: 'cost',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '{{ currency() }} ' + parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            @can('shippings.change-status')
                            const isChecked = data === 1 || data === true ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            return `
                                <div class="form-check form-switch  form-switch-primary form-switch-sm d-flex justify-content-center">
                                    <input type="checkbox" class="form-check-input status-switcher" 
                                        id="${switchId}" data-id="${row.id}" ${isChecked}>
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            `;
                            @else
                            if (data === 1 || data === true) {
                                return '<span class="badge badge-success badge-round badge-lg">{{ trans('shipping.active') }}</span>';
                            } else {
                                return '<span class="badge badge-danger badge-round badge-lg">{{ trans('shipping.inactive') }}</span>';
                            }
                            @endcan
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let editUrl = "{{ route('admin.shippings.edit', ':id') }}".replace(':id', row.id);
                            let showUrl = "{{ route('admin.shippings.show', ':id') }}".replace(':id', row.id);
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    @can('shippings.show')
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    @endcan
                                    @can('shippings.edit')
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('shipping.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    @endcan
                                    @can('shippings.delete')
                                    <button type="button"
                                    class="btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-shipping"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.name}"
                                    title="{{ trans('shipping.delete') }}">
                                        <i class="uil uil-trash table_action_icon"></i>
                                    </button>
                                    @endcan
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "{{ __('common.search') }}:",
                }
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Live search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                    updateUrlParams();
                }, 500);
            });

            // Search button click
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Filter change handlers
            $('#active, #created_from_filter, #created_until_filter').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val('');
                $('#created_from_filter').val('');
                $('#created_until_filter').val('');
                table.ajax.reload();
                // Clear URL parameters
                window.history.replaceState({}, '', window.location.pathname);
            });

            // Update URL parameters function
            function updateUrlParams() {
                const params = new URLSearchParams();
                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#active').val()) params.set('active', $('#active').val());
                if ($('#created_from_filter').val()) params.set('created_from', $('#created_from_filter').val());
                if ($('#created_until_filter').val()) params.set('created_until', $('#created_until_filter').val());

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }

            // Change shipping status
            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const id = switcher.data('id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                switcher.prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.shippings.change-status', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        switcher.prop('disabled', false);
                        if (response.status) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            switcher.prop('checked', !switcher.is(':checked'));
                        }
                    },
                    error: function() {
                        switcher.prop('disabled', false);
                        switcher.prop('checked', !switcher.is(':checked'));
                        toastr.error('{{ trans('shipping.error_changing_status') }}');
                    }
                });
            });

            // Delete shipping
            $(document).on('click', '#confirmDeleteShippingBtn', function() {
                let id = $(this).data('item-id');
                let baseUrl = "{{ route('admin.shippings.index') }}";
                $.ajax({
                    url: baseUrl + '/' + id,
                    type: 'POST',
                    data: { 
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        $('#modal-delete-shipping').modal('hide');
                        if (response.success || response.status) {
                            toastr.success(response.message || '{{ trans('shipping.deleted_successfully') }}');
                            table.ajax.reload();
                        } else {
                            toastr.error(response.message || '{{ trans('shipping.error_deleting') }}');
                        }
                    },
                    error: function(xhr) {
                        $('#modal-delete-shipping').modal('hide');
                        let message = '{{ trans('shipping.error_deleting') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                    }
                });
            });

            // Set item data when modal is shown
            $('#modal-delete-shipping').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const itemId = button.data('item-id');
                const itemName = button.data('item-name');
                
                $('#delete-shipping-name').text(itemName);
                $('#confirmDeleteShippingBtn').data('item-id', itemId);
            });
        });
    </script>
@endpush
