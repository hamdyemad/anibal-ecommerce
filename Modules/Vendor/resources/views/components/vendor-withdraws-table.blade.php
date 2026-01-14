@props(['vendor'])

<div class="card card-holder mt-3">
    <div class="card-header">
        <h3>
            <i class="uil uil-money-withdraw me-1"></i>{{ trans('vendor::vendor.vendor_withdraws') }}
            <span class="badge bg-white badge-round badge-lg ms-2" id="withdraws-count">0</span>
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="userDatatable global-shadow border-light-0 bg-white w-100">
            <div class="table-responsive">
                <table id="vendorWithdrawsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                    <thead>
                        <tr class="userDatatable-header">
                            <th class="text-center"><span class="userDatatable-title">#</span></th>
                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.withdraw_information') }}</span></th>
                            <th class="text-center"><span class="userDatatable-title">{{ trans('vendor::vendor.invoice') }}</span></th>
                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.sent_by') }}</span></th>
                            <th><span class="userDatatable-title">{{ trans('common.created_at') }}</span></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Vendor Withdraws DataTable
        let withdrawsTable = $('#vendorWithdrawsDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.vendors.withdraws-datatable', $vendor->id) }}',
                type: 'GET'
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'text-center fw-bold'
                },
                {
                    data: null,
                    name: 'withdraw_information',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let html = '<div class="d-flex flex-column gap-1">';
                        html += `<div><span class="text-muted fw-bold">{{ trans('vendor::vendor.balance_before') }}:</span> <strong>${parseFloat(row.balance_before).toFixed(2)} {{ currency() }}</strong></div>`;
                        html += `<div><span class="text-muted fw-bold">{{ trans('vendor::vendor.sent_amount') }}:</span> <strong class="text-success">${parseFloat(row.sent_amount).toFixed(2)} {{ currency() }}</strong></div>`;
                        html += `<div><span class="text-muted fw-bold">{{ trans('vendor::vendor.balance_after') }}:</span> <strong>${parseFloat(row.balance_after).toFixed(2)} {{ currency() }}</strong></div>`;
                        html += `<div><span class="text-muted fw-bold">{{ trans('vendor::vendor.withdraw_status') }}:</span> `;
                        
                        if (row.status === 'accepted') {
                            html += '<strong class="text-success">{{ trans('vendor::vendor.accepted') }}</strong>';
                        } else if (row.status === 'rejected') {
                            html += '<strong class="text-danger">{{ trans('vendor::vendor.rejected') }}</strong>';
                        } else {
                            html += '<strong class="text-primary">{{ trans('vendor::vendor.new') }}</strong>';
                        }
                        
                        html += '</div></div>';
                        return html;
                    }
                },
                {
                    data: 'invoice',
                    name: 'invoice',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data) {
                        if (data) {
                            return `<a href="${data}" target="_blank" class="btn btn-sm btn-primary"><i class="uil uil-download-alt m-0"></i></a>`;
                        }
                        return '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'sent_by',
                    name: 'sent_by',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: false,
                    searchable: false
                }
            ],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [[0, 'desc']],
            language: {
                lengthMenu: "{{ trans('common.show') }} _MENU_",
                info: "{{ trans('common.showing') }} _START_ {{ trans('common.to') }} _END_ {{ trans('common.of') }} _TOTAL_ {{ trans('common.entries') }}",
                infoEmpty: "{{ trans('common.showing') }} 0 {{ trans('common.to') }} 0 {{ trans('common.of') }} 0 {{ trans('common.entries') }}",
                infoFiltered: "({{ trans('common.filtered_from') }} _MAX_ {{ trans('common.total_entries') }})",
                zeroRecords: "{{ trans('vendor::vendor.no_withdraws_found') }}",
                emptyTable: "{{ trans('vendor::vendor.no_withdraws_found') }}",
                loadingRecords: "{{ trans('common.loading') }}...",
                processing: "{{ trans('common.processing') }}...",
                paginate: {
                    @if(app()->getLocale() == 'en')
                        first: '<i class="uil uil-angle-double-left"></i>',
                        last: '<i class="uil uil-angle-double-right"></i>',
                        next: '<i class="uil uil-angle-right"></i>',
                        previous: '<i class="uil uil-angle-left"></i>'
                    @else
                        first: '<i class="uil uil-angle-double-right"></i>',
                        last: '<i class="uil uil-angle-double-left"></i>',
                        next: '<i class="uil uil-angle-left"></i>',
                        previous: '<i class="uil uil-angle-right"></i>'
                    @endif
                }
            },
            drawCallback: function(settings) {
                // Update withdraws count badge
                $('#withdraws-count').text(settings.json.recordsFiltered || 0);
            }
        });
    });
</script>
@endpush
