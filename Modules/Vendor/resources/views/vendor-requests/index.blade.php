@extends('layout.app')

@section('title')
    Vendor Requests | Bnaia
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
                    ['title' => 'Vendor Requests'],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">Vendor Requests Management</h4>
                    </div>

                    <div class="alert alert-info glowing-alert" role="alert">
                        {{ __('common.live_search_info') }}
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
                                                <small class="text-muted">({{ __('common.real_time') ?? 'Real-time' }})</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="Search by email or company name"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                Status
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status">
                                                <option value="">All</option>
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approved</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared"
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

            // Get status from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const statusParam = urlParams.get('status');

            // Set status filter if present in URL
            if (statusParam) {
                $('#status').val(statusParam);
                console.log('Setting status filter from URL:', statusParam);
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
                            return row.activities.map(a => `<span class="badge bg-info">${a.name}</span>`).join(' ');
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
                            return `<span class="badge bg-${color} text-capitalize">${row.status}</span>`;
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

            // Draw table with status filter if present in URL
            if (statusParam) {
                setTimeout(function() {
                    table.draw();
                }, 100);
            }

            // Initialize Select2 on custom entries select
            if ($.fn.select2) {
                $('#entriesSelect').select2({
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

            // Search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            $('#search').on('change', function() {
                clearTimeout(searchTimer);
                table.ajax.reload();
            });

            // Server-side filter event listeners - reload data when filters change
            $('#status').on('change', function() {
                console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                table.ajax.reload();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#status').val('');
                // Reload table
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
