@props(['vendor'])

<div class="card card-holder mt-3">
    <div class="card-header">
        <h3>
            <i class="uil uil-shopping-bag me-1"></i>{{ trans('vendor::vendor.vendor_order_products') }}
            <span class="badge bg-white badge-round badge-lg ms-2" id="order-products-count">0</span>
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="userDatatable global-shadow border-light-0 bg-white w-100">
            <div class="table-responsive">
                <table id="vendorOrderProductsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                    <thead>
                        <tr class="userDatatable-header">
                            <th class="text-center"><span class="userDatatable-title">#</span></th>
                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.order_product_information') }}</span></th>
                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.price_before_taxes') }}</span></th>
                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.taxes') }}</span></th>
                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.price_including_tax') }}</span></th>
                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.quantity') }}</span></th>
                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.total_price') }}</span></th>
                            <th class="text-center"><span class="userDatatable-title">{{ trans('common.actions') }}</span></th>
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
        // Initialize Vendor Order Products DataTable
        let orderProductsTable = $('#vendorOrderProductsDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.vendors.order-products-datatable', $vendor->id) }}',
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
                    name: 'order_product_information',
                    orderable: false,
                    searchable: true,
                    render: function(data, type, row) {
                        // Helper function to truncate string
                        function truncateStr(str, maxLength = 30) {
                            if (!str) return '';
                            str = $('<div/>').text(str).html();
                            return str.length > maxLength ? str.substring(0, maxLength) + '...' : str;
                        }
                        
                        let html = '<div class="d-flex align-items-start gap-2">';
                        
                        // Product Image
                        if (row.product_image) {
                            html += `<img src="${row.product_image}" alt="${$('<div/>').text(row.product_name).html()}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">`;
                        } else {
                            html += '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="uil uil-image text-muted"></i></div>';
                        }
                        
                        html += '<div class="d-flex flex-column gap-1">';
                        html += `<div><span class="text-muted fw-bold">{{ trans('vendor::vendor.order_number') }}:</span> <strong>#${$('<div/>').text(row.order_number).html()}</strong></div>`;
                        html += `<div title="${$('<div/>').text(row.product_name).html()}"><span class="text-muted fw-bold">{{ trans('vendor::vendor.product') }}:</span> <strong>${truncateStr(row.product_name, 30)}</strong></div>`;
                        html += `<div><span class="text-muted fw-bold">{{ trans('vendor::vendor.sku') }}:</span> <code>${truncateStr(row.sku, 20)}</code></div>`;
                        
                        if (row.variant_name) {
                            html += `<div title="${$('<div/>').text(row.variant_name).html()}"><span class="text-muted fw-bold">{{ trans('vendor::vendor.variant') }}:</span> <span class="badge badge-primary badge-round badge-lg">${truncateStr(row.variant_name, 25)}</span></div>`;
                        }
                        
                        if (row.order_stage) {
                            html += `<div><span class="text-muted fw-bold">{{ trans('vendor::vendor.order_status') }}:</span> <span class="badge badge-round badge-lg" style="background-color: ${row.order_stage.color}; color: #fff;">${$('<div/>').text(row.order_stage.name).html()}</span></div>`;
                        }
                        
                        html += '</div></div>';
                        return html;
                    }
                },
                {
                    data: 'price_before_tax',
                    name: 'price_before_tax',
                    orderable: false,
                    render: function(data) {
                        return `${parseFloat(data).toFixed(2)} {{ currency() }}`;
                    }
                },
                {
                    data: null,
                    name: 'taxes',
                    orderable: false,
                    render: function(data, type, row) {
                        if (row.taxes_detail && row.taxes_detail.length > 0) {
                            let html = `<span class="badge badge-round badge-lg bg-primary mb-1">{{ trans('order::order.total') }}: ${row.tax_percentage}%</span><div>`;
                            row.taxes_detail.forEach(function(tax) {
                                html += `<span class="badge badge-round badge-lg bg-secondary me-1 mb-1">${$('<div/>').text(tax.name).html()} ${tax.percentage}%</span>`;
                            });
                            html += '</div>';
                            return html;
                        }
                        return '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'price_with_tax',
                    name: 'price_with_tax',
                    orderable: false,
                    render: function(data) {
                        return `${parseFloat(data).toFixed(2)} {{ currency() }}`;
                    }
                },
                {
                    data: 'quantity',
                    name: 'quantity',
                    orderable: false,
                    className: 'fw-medium'
                },
                {
                    data: 'total_price',
                    name: 'total_price',
                    orderable: false,
                    className: 'fw-bold text-success',
                    render: function(data) {
                        return `${parseFloat(data).toFixed(2)} {{ currency() }}`;
                    }
                },
                {
                    data: 'order_id',
                    name: 'order_id',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data) {
                        const showUrl = "{{ route('admin.orders.show', ':id') }}".replace(':id', data);
                        return `<a href="${showUrl}" target="_blank" class="btn btn-sm btn-primary"><i class="uil uil-eye m-0"></i></a>`;
                    }
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
                zeroRecords: "{{ trans('vendor::vendor.no_order_products_found') }}",
                emptyTable: "{{ trans('vendor::vendor.no_order_products_found') }}",
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
                // Update order products count badge
                $('#order-products-count').text(settings.json.recordsFiltered || 0);
            }
        });
    });
</script>
@endpush
