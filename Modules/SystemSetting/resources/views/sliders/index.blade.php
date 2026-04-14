@extends('layout.app')

@section('title')
    {{ __('systemsetting::sliders.sliders_management') }}
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
                    ['title' => __('systemsetting::sliders.sliders_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('systemsetting::sliders.sliders_management') }}</h4>
                        <div class="d-flex gap-2">
                            @can('sliders.create')
                            <a href="{{ route('admin.system-settings.sliders.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ __('systemsetting::sliders.create_slider') }}
                            </a>
                            @endcan
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
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('common.search') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Created Date From --}}
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('systemsetting::sliders.created_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>

                                    {{-- Created Date To --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('systemsetting::sliders.created_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ __('systemsetting::sliders.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('systemsetting::sliders.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('systemsetting::sliders.reset_filters') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('systemsetting::sliders.reset_filters') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('systemsetting::sliders.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('systemsetting::sliders.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="slidersDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::sliders.media') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::sliders.title') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::sliders.description') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::sliders.slider_link') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::sliders.created_at') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::sliders.actions') }}</span></th>
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
    <x-delete-modal modalId="modal-delete-slider" :title="__('systemsetting::sliders.confirm_delete')"
        :message="__('systemsetting::sliders.delete_confirmation')" itemNameId="delete-slider-name"
        confirmBtnId="confirmDeleteSliderBtn" deleteRoute="{{ rtrim(route('admin.system-settings.sliders.index'), '/') }}"
        :cancelText="__('systemsetting::sliders.cancel')" :deleteText="__('systemsetting::sliders.delete')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        $('#search').val(urlParams.get('search'));
    }
    if (urlParams.has('active')) {
        $('#active').val(urlParams.get('active'));
    }
    if (urlParams.has('created_date_from')) {
        $('#created_date_from').val(urlParams.get('created_date_from'));
    }
    if (urlParams.has('created_date_to')) {
        $('#created_date_to').val(urlParams.get('created_date_to'));
    }

    let table = $('#slidersDataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.system-settings.sliders.datatable') }}',
            type: 'GET',
            data: function(d) {
                d.active = $('#active').val();
                d.search = $('#search').val();
                d.created_date_from = $('#created_date_from').val();
                d.created_date_to = $('#created_date_to').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'image_preview', name: 'image_preview', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'description', name: 'description' },
            { data: 'link_display', name: 'link_display', orderable: false },
            { data: 'created_date', name: 'created_date', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']],
        pagingType: 'full_numbers',
        dom: '<"row"<"col-sm-12"tr>>' + '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        searching: false,
        language: {
            lengthMenu: "{{ __('systemsetting::sliders.show') }} _MENU_",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            emptyTable: "{{ __('systemsetting::sliders.no_data_available') }}",
            zeroRecords: "{{ __('systemsetting::sliders.no_sliders_found') }}",
            loadingRecords: "{{ __('systemsetting::sliders.loading') }}",
            processing: "{{ __('systemsetting::sliders.processing') }}",
            search: "{{ __('systemsetting::sliders.search') }}:",
        }
    });

    if ($.fn.select2) {
        $('#entriesSelect, #active').select2({
            theme: 'bootstrap-5',
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
    }

    $('#entriesSelect').on('change', function() {
        table.page.len($(this).val()).draw();
    });

    function updateUrlWithFilters() {
        const params = new URLSearchParams();
        const search = $('#search').val();
        const active = $('#active').val();
        const createdDateFrom = $('#created_date_from').val();
        const createdDateTo = $('#created_date_to').val();

        if (search) params.set('search', search);
        if (active) params.set('active', active);
        if (createdDateFrom) params.set('created_date_from', createdDateFrom);
        if (createdDateTo) params.set('created_date_to', createdDateTo);

        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
    }

    $('#searchBtn').on('click', function() {
        updateUrlWithFilters();
        table.draw();
    });

    let searchTimer;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            updateUrlWithFilters();
            table.draw();
        }, 500);
    });

    $('#active, #created_date_from, #created_date_to').on('change', function() {
        updateUrlWithFilters();
        table.draw();
    });

    $('#resetFilters').on('click', function() {
        $('#search').val('');
        $('#active').val('').trigger('change');
        $('#created_date_from').val('');
        $('#created_date_to').val('');
        updateUrlWithFilters();
        table.draw();
    });

    $('#modal-delete-slider').on('hidden.bs.modal', function() {
        if (window.deleteSuccess) {
            table.draw();
            window.deleteSuccess = false;
        }
    });
});
</script>
@endpush
