@extends('layout.app')

@section('title')
    {{ __('vendor::vendor.vendor_requests_management') }}
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
                    ['title' => __('vendor::vendor.vendor_requests_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-clipboard-notes me-2"></i>
                            {{ __('vendor::vendor.vendor_requests_management') }}
                        </h4>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="{{ __('vendor::vendor.search_by_email_or_company') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('common.status') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status">
                                                <option value="">{{ __('common.all') }}</option>
                                                <option value="pending">{{ __('common.pending') }}</option>
                                                <option value="approved">{{ __('common.approved') }}</option>
                                                <option value="rejected">{{ __('common.rejected') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="activity_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-briefcase me-1"></i>
                                                {{ __('vendor::vendor.activity') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="activity_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach($activities as $activity)
                                                    <option value="{{ $activity['id'] }}">{{ $activity['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('common.created_date_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('common.created_date_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
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
                                        <button type="button" id="exportExcel"
                                            class="btn btn-primary btn-default btn-squared"
                                            title="{{ __('common.excel') }}">
                                            <i class="uil uil-file-download-alt me-1"></i>
                                            {{ __('common.export_excel') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
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

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="vendorRequestsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">Company Information</span></th>
                                    <th><span class="userDatatable-title">Contact</span></th>
                                    <th><span class="userDatatable-title">Activities</span></th>
                                    <th><span class="userDatatable-title">Status</span></th>
                                    <th><span class="userDatatable-title">Rejection Reason</span></th>
                                    <th><span class="userDatatable-title">Date</span></th>
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

    {{-- Confirmation Modal --}}
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="confirmActionForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmActionLabel">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="confirmMessage">Are you sure?</p>
                        <div id="rejectReasonDiv" style="display: none;">
                            <label for="rejectReason" class="form-label">Rejection Reason:</label>
                            <input type="text" class="form-control" id="rejectReason" name="reason" placeholder="Enter rejection reason...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="confirmActionBtn">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('Vendor Requests page loaded, initializing DataTable...');

            let per_page = 10;

            // Get filters from URL parameters
            const urlParams = new URLSearchParams(window.location.search);

            // Populate filters from URL parameters on page load
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('status')) $('#status').val(urlParams.get('status'));
            if (urlParams.has('activity_id')) $('#activity_filter').val(urlParams.get('activity_id'));
            if (urlParams.has('created_date_from')) $('#created_date_from').val(urlParams.get('created_date_from'));
            if (urlParams.has('created_date_to')) $('#created_date_to').val(urlParams.get('created_date_to'));

            // Function to update URL with current filters
            function updateUrlWithFilters() {
                const params = new URLSearchParams();

                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#status').val()) params.set('status', $('#status').val());
                if ($('#activity_filter').val()) params.set('activity_id', $('#activity_filter').val());
                if ($('#created_date_from').val()) params.set('created_date_from', $('#created_date_from').val());
                if ($('#created_date_to').val()) params.set('created_date_to', $('#created_date_to').val());

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }

            // Server-side processing with pagination
            let table = $('#vendorRequestsDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '{{ route('admin.vendor-requests.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        // Add filter parameters
                        d.search = $('#search').val();
                        d.status = $('#status').val();
                        d.activity_id = $('#activity_filter').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            d.orderColumnIndex = d.order[0].column;
                            d.orderDirection = d.order[0].dir;
                        }
                        console.log('📤 Sending request:', d);
                        return d;
                    },
                    dataSrc: function(json) {
                        console.log('📦 Data received from server:', json);
                        console.log('Total records:', json.total);
                        console.log('Filtered records:', json.recordsFiltered);
                        console.log('Current page:', json.current_page);

                        // Map backend response to DataTables format
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;

                        if (json.error) {
                            console.error('❌ Server returned error:', json.error);
                            alert('Error: ' + json.error);
                            return [];
                        }
                        if (!json.data || json.data.length === 0) {
                            console.warn('⚠️ No data returned from server');
                        }
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.error('❌ DataTables AJAX Error:', {
                            xhr: xhr,
                            error: error,
                            code: code
                        });
                        console.error('Response Status:', xhr.status);
                        console.error('Response Text:', xhr.responseText);

                        // Try to parse JSON error
                        let errorMsg = 'Error loading data. Status: ' + xhr.status;
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                console.error('❌ Server Error:', response.error);
                                errorMsg += '\n\nError: ' + response.error;
                                if (response.trace) {
                                    console.error('Stack Trace:', response.trace);
                                }
                            }
                        } catch (e) {
                            console.error('Could not parse error response');
                        }

                        alert(errorMsg + '\n\nCheck console for full details.');
                    }
                },
                columns: [
                    // Row number column
                    {
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row) {
                            return row.row_number
                        }
                    },
                    // Company Information column
                    {
                        data: null,
                        name: 'company_info',
                        orderable: false,
                        render: function(data, type, row) {
                            let html = `
                                <div class="vendor-card p-2 bg-light-subtle rounded-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div>
                                            <div class="fw-semibold text-dark text-capitalize">
                                                ${$('<div>').text(row.company_name).html()}
                                            </div>
                                            <small class="text-muted">${$('<div>').text(row.email).html()}</small>
                                        </div>
                                    </div>
                            `;
                            html += `</div></div>`;
                            return html;
                        }
                    },

                    // Contact column
                    {
                        data: 'phone',
                        name: 'phone',
                        orderable: false,
                        render: function(data) {
                            return '<div class="userDatatable-content"><span>' + data + '</span></div>';
                        }
                    },

                    // Activities column
                    {
                        data: 'activities',
                        name: 'activities',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!row.activities || row.activities.length === 0) {
                                return '<span class="badge bg-secondary">None</span>';
                            }
                            return row.activities.map(a => `<span class="badge bg-info badge-lg badge-round">${a.name}</span>`).join(' ');
                        }
                    },

                    // Status column
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        render: function(data, type, row) {
                            const statusColors = {
                                'pending': 'warning',
                                'approved': 'success',
                                'rejected': 'danger'
                            };
                            const color = statusColors[row.status] || 'secondary';
                            return `<span class="badge bg-${color} badge-lg badge-round text-capitalize">${row.status}</span>`;
                        }
                    },

                    // Rejection Reason column
                    {
                        data: 'rejection_reason',
                        name: 'rejection_reason',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!data || data.trim() === '') {
                                return '<span class="text-muted">-</span>';
                            }
                            return `<span title="${$('<div>').text(data).html()}">${$('<div>').text(data).html()}</span>`;
                        }
                    },

                    // Date column
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        render: function(data, type, row) {
                            return row.created_at;
                        }
                    },

                    // Actions column
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let actions = `<div class="orderDatatable_actions d-inline-flex gap-1">`;

                            // Pending: Show Approve and Reject buttons
                            if (row.status === 'pending') {
                                actions += `
                                <a href="javascript:void(0);"
                                class="approve btn btn-success table_action_father approve-btn"
                                data-id="${row.id}"
                                title="Approve">
                                    <i class="uil uil-check table_action_icon"></i>
                                </a>
                                <a href="javascript:void(0);"
                                class="reject btn btn-warning table_action_father reject-btn"
                                data-id="${row.id}"
                                title="Reject">
                                    <i class="uil uil-times table_action_icon"></i>
                                </a>
                                `;
                            }
                            // Approved: Show Reject and Archive buttons
                            else if (row.status === 'approved') {
                                actions += `
                                <a href="javascript:void(0);"
                                class="reject btn btn-warning table_action_father reject-btn"
                                data-id="${row.id}"
                                title="Reject">
                                    <i class="uil uil-times table_action_icon"></i>
                                </a>
                                <a href="javascript:void(0);"
                                class="archive btn btn-danger table_action_father archive-btn"
                                data-id="${row.id}"
                                title="Archive">
                                    <i class="uil uil-archive table_action_icon"></i>
                                </a>
                                `;
                            }
                            // Rejected: Show Archive button only
                            else if (row.status === 'rejected') {
                                actions += `
                                <a href="javascript:void(0);"
                                class="archive btn btn-danger table_action_father archive-btn"
                                data-id="${row.id}"
                                title="Archive">
                                    <i class="uil uil-archive table_action_icon"></i>
                                </a>
                                `;
                            }

                            actions += `</div>`;
                            return actions;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                order: [
                    [0, 'desc']
                ],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                searching: false, // Disable built-in search (using custom)
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "No vendor requests found",
                    emptyTable: "No vendor requests found",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    search: "{{ __('common.search') ?? 'Search' }}:",
                    aria: {
                        sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                        sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                    }
                }
            });

            // Initialize Select2 on filter dropdowns
            if ($.fn.select2) {
                $('#entriesSelect, #status, #activity_filter').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            } else {
                console.error('Select2 is not loaded');
            }

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Real-time search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    updateUrlWithFilters();
                    table.ajax.reload();
                }, 500);
            });

            // Search button click handler
            $('#searchBtn').on('click', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Filter change handlers - real-time filtering
            $('#status, #activity_filter').on('change', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Date filter change handlers
            $('#created_date_from, #created_date_to').on('change', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Export Excel button
            $('#exportExcel').on('click', function() {
                alert('{{ __('common.export_excel') }} feature coming soon');
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#status').val('').trigger('change');
                $('#activity_filter').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                // Update URL and reload table
                updateUrlWithFilters();
                table.ajax.reload();
            });

            let pendingAction = null;
            const confirmModalElement = document.getElementById('confirmActionModal');
            let confirmModalInstance = new bootstrap.Modal(confirmModalElement, {
                backdrop: 'static',
                keyboard: false
            });

            // Reset modal when closed
            confirmModalElement.addEventListener('hidden.bs.modal', function() {
                setTimeout(function() {
                    pendingAction = null;
                    $('#rejectReason').val('');
                    $('#confirmActionBtn').prop('disabled', false).text('Confirm');
                }, 100);
            });

            // Approve vendor request
            $(document).on('click', '.approve-btn', function() {
                pendingAction = {
                    type: 'approve',
                    requestId: $(this).data('id')
                };
                $('#confirmMessage').text('Are you sure you want to approve this vendor request?');
                $('#rejectReasonDiv').hide();
                $('#rejectReason').val('');
                confirmModalInstance.show();
            });

            // Reject vendor request
            $(document).on('click', '.reject-btn', function() {
                pendingAction = {
                    type: 'reject',
                    requestId: $(this).data('id')
                };
                $('#confirmMessage').text('Are you sure you want to reject this vendor request?');
                $('#rejectReasonDiv').show();
                $('#rejectReason').val('');
                confirmModalInstance.show();
            });

            // Archive vendor request
            $(document).on('click', '.archive-btn', function() {
                pendingAction = {
                    type: 'archive',
                    requestId: $(this).data('id')
                };
                $('#confirmMessage').text('Are you sure you want to archive this vendor request?');
                $('#rejectReasonDiv').hide();
                $('#rejectReason').val('');
                confirmModalInstance.show();
            });

            // Handle form submission
            $('#confirmActionForm').on('submit', function(e) {
                e.preventDefault();

                if (!pendingAction) {
                    console.warn('No pending action');
                    return;
                }

                const $btn = $('#confirmActionBtn');
                const originalText = $btn.text();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

                // Show loading overlay
                const loadingOverlay = document.querySelector('.loading-overlay');
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'flex';
                }

                const form = document.getElementById('confirmActionForm');

                // Remove any existing _method input
                const existingMethodInput = form.querySelector('input[name="_method"]');
                if (existingMethodInput) {
                    existingMethodInput.remove();
                }

                if (pendingAction.type === 'approve') {
                    form.action = '{{ route('admin.vendor-requests.index') }}/' + pendingAction.requestId + '/approve';
                    form.method = 'POST';
                    console.log('Submitting approve form to:', form.action);
                    form.submit();
                } else if (pendingAction.type === 'reject') {
                    form.action = '{{ route('admin.vendor-requests.index') }}/' + pendingAction.requestId + '/reject';
                    form.method = 'POST';
                    console.log('Submitting reject form to:', form.action);
                    form.submit();
                } else if (pendingAction.type === 'archive') {
                    form.action = '{{ route('admin.vendor-requests.index') }}/' + pendingAction.requestId;
                    form.method = 'POST';
                    // Add hidden input for DELETE method
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    console.log('Submitting archive form to:', form.action);
                    form.submit();
                }
            });
        });
    </script>
@endpush
