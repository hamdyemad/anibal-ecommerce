@extends('layout.app')

@push('styles')
<!-- Select2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('activity.activities_management')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('activity.activities_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.activities.create') }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ __('activity.add_activity') }}
                            </a>
                        </div>
                    </div>

                    {{-- Search and Filter --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="search" class="il-gray fs-14 fw-500 mb-10">{{ __('common.search') }}</label>
                                                <input type="text" 
                                                       class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                       id="search" 
                                                       placeholder="{{ __('activity.search_by_name') }}"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="active" class="il-gray fs-14 fw-500 mb-10">{{ __('activity.activation') }}</label>
                                                <select class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                        id="active">
                                                    <option value="">{{ __('activity.all') }}</option>
                                                    <option value="1">{{ __('activity.active') }}</option>
                                                    <option value="0">{{ __('activity.inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_date_from') }}</label>
                                                <input type="date" 
                                                       class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                       id="created_date_from">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_date_to') }}</label>
                                                <input type="date" 
                                                       class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                       id="created_date_to">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10">&nbsp;</label>
                                                <button type="button" id="exportExcel" class="btn btn-success btn-default btn-squared w-100" title="{{ __('common.excel') }}">
                                                    <i class="uil uil-file-download-alt"></i> <span class="d-none d-lg-inline">{{ __('common.excel') }}</span>
                                                </button>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page Selector --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('common.show') ?? 'Show' }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('common.entries') ?? 'entries' }}</label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="activitiesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th>
                                        <span class="userDatatable-title">#</span>
                                    </th>
                                    @foreach($languages as $language)
                                        <th>
                                            <span class="userDatatable-title" @if($language->rtl) dir="rtl" @endif>
                                                {{ __('activity.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach
                                    <th>
                                        <span class="userDatatable-title">{{ __('activity.activation') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ __('activity.created_at') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ __('common.actions') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Delete Confirmation Modal Component --}}
    <x-delete-modal 
        modalId="modal-delete-activity"
        :title="__('activity.confirm_delete')"
        :message="__('activity.delete_confirmation')"
        itemNameId="delete-activity-name"
        confirmBtnId="confirmDeleteBtn"
        :deleteRoute="route('admin.activities.index')"
        :cancelText="__('activity.cancel')"
        :deleteText="__('activity.delete_activity')"
    />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Load all data initially from server, then cache for client-side operations
        var table = $('#activitiesDataTable').DataTable({
            processing: true,
            serverSide: false, // Client-side processing after initial load
            ajax: {
                url: '{{ route('admin.activities.datatable') }}',
                type: 'GET',
                error: function(xhr, error, code) {
                    console.log('DataTables Error:', xhr, error, code);
                    alert('Error loading data. Please check console for details.');
                }
            },
            columns: [
                { data: 0, name: 'id' }, // #
                @foreach($languages as $language)
                { data: {{ $loop->index + 1 }}, name: 'name_{{ $language->code }}', render: function(data) { return data; } },
                @endforeach
                { data: {{ count($languages) + 1 }}, name: 'active', render: function(data) { return data; } }, // Active Status
                { data: {{ count($languages) + 2 }}, name: 'created_at', render: function(data) { return data; } }, // Created At
                { data: {{ count($languages) + 3 }}, name: 'actions', orderable: false, searchable: false, render: function(data) { return data; } } // Actions
            ],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[0, 'asc']],
            pagingType: 'full_numbers',
            dom: '<"row"<"col-sm-12"tr>>' +
                 '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '{{ __('activity.activities_management') }}'
                }
            ],
            searching: true, // Enable built-in search
            language: {
                lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                zeroRecords: "{{ __('activity.no_activities_found') ?? 'No activities found' }}",
                emptyTable: "{{ __('activity.no_activities_found') ?? 'No activities found' }}",
                loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                processing: "{{ __('common.processing') ?? 'Processing' }}...",
                search: "{{ __('common.search') ?? 'Search' }}:",
                paginate: {
                    first: '{{ __('common.first') ?? 'First' }}',
                    last: '{{ __('common.last') ?? 'Last' }}',
                    next: '{{ __('common.next') ?? 'Next' }}',
                    previous: '{{ __('common.previous') ?? 'Previous' }}'
                },
                aria: {
                    sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                    sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                }
            }
        });

        // Initialize Select2 on custom entries select
        if ($.fn.select2) {
            $('#entriesSelect').select2({
                theme: 'bootstrap-5',
                minimumResultsForSearch: Infinity,
                width: 'auto'
            });
        }

        // Handle entries select change
        $('#entriesSelect').on('change', function() {
            table.page.len($(this).val()).draw();
        });

        // Handle Excel export button
        $('#exportExcel').on('click', function() {
            table.button('.buttons-excel').trigger();
        });

        // Search on cached data with debounce
        let searchTimer;
        $('#search').on('keyup', function() {
            clearTimeout(searchTimer);
            const searchValue = $(this).val();
            searchTimer = setTimeout(function() {
                table.search(searchValue).draw(); // Search on cached data
            }, 500);
        });
        
        $('#search').on('change', function() {
            clearTimeout(searchTimer);
            table.search($(this).val()).draw();
        });

        // Custom filter function for active status and dates on cached data
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                // Only apply to activities table
                if (settings.nTable.id !== 'activitiesDataTable') {
                    return true;
                }
                
                var activeFilter = $('#active').val();
                var dateFrom = $('#created_date_from').val();
                var dateTo = $('#created_date_to').val();
                
                // Active filter (column {{ count($languages) + 1 }})
                if (activeFilter && activeFilter !== '') {
                    var colIndex = {{ count($languages) + 1 }};
                    
                    // Get the actual rendered cell content (with HTML)
                    var rowNode = table.row(dataIndex).node();
                    if (!rowNode) {
                        return true;
                    }
                    
                    var cells = $(rowNode).find('td');
                    if (cells.length <= colIndex) {
                        return true;
                    }
                    
                    // Get the HTML content of the cell
                    var cellHtml = $(cells[colIndex]).html();
                    
                    if (!cellHtml) {
                        return true;
                    }
                    
                    // Check if the cell contains the success badge (active) or danger badge (inactive)
                    var isActiveRow = cellHtml.indexOf('badge-success') > -1;
                    var isInactiveRow = cellHtml.indexOf('badge-danger') > -1;
                    
                    // Filter logic
                    if (activeFilter === '1') {
                        // Show only active rows (must have badge-success)
                        return isActiveRow;
                    } else if (activeFilter === '0') {
                        // Show only inactive rows (must have badge-danger)
                        return isInactiveRow;
                    }
                }
                
                // Date filters (column {{ count($languages) + 2 }})
                if (dateFrom || dateTo) {
                    var dateColumn = data[{{ count($languages) + 2 }}];
                    if (dateColumn) {
                        var rowDate = dateColumn.replace(/<[^>]*>/g, '').trim().split(' ')[0]; // Extract YYYY-MM-DD
                        if (dateFrom && rowDate < dateFrom) return false;
                        if (dateTo && rowDate > dateTo) return false;
                    }
                }
                
                return true;
            }
        );

        // Trigger redraw when filters change
        $('#active, #created_date_from, #created_date_to').on('change', function() {
            table.draw();
        });
    });
</script>
@endpush
