@props(['vendor'])

<div class="card card-holder mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <i class="uil uil-box me-1"></i>{{ trans('catalogmanagement::product.products') }}
            <span class="badge bg-white badge-round badge-lg ms-2" id="products-count">0</span>
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="userDatatable global-shadow border-light-0 bg-white w-100">
            <div class="table-responsive">
                <table id="vendorProductsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                    <thead>
                        <tr class="userDatatable-header">
                            <th class="text-center"><span class="userDatatable-title">#</span></th>
                            <th><span class="userDatatable-title">{{ trans('catalogmanagement::product.product_information') }}</span></th>
                            <th class="text-center"><span class="userDatatable-title">{{ trans('catalogmanagement::product.approval_status') }}</span></th>
                            <th class="text-center"><span class="userDatatable-title">{{ trans('common.activation') }}</span></th>
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
        // Initialize Vendor Products DataTable
        let productsTable = $('#vendorProductsDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.vendors.products-datatable', $vendor->id) }}',
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
                    name: 'product_information',
                    orderable: false,
                    searchable: true,
                    render: function(data, type, row) {
                        // Helper function to truncate string
                        function truncateStr(str, maxLength = 30) {
                            if (!str) return '';
                            str = $('<div/>').text(str).html();
                            return str.length > maxLength ? str.substring(0, maxLength) + '...' : str;
                        }
                        
                        let html = '<div class="product-info-container">';
                        
                        // Product Names
                        if (row.product_name_en && row.product_name_en !== '-') {
                            html += `<div class="product-name-item mb-2" title="${$('<div/>').text(row.product_name_en).html()}">
                                <span class="language-badge badge bg-primary text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">EN</span>
                                <span class="product-name text-dark fw-semibold">${truncateStr(row.product_name_en, 35)}</span>
                            </div>`;
                        }
                        
                        if (row.product_name_ar && row.product_name_ar !== '-') {
                            html += `<div class="product-name-item mb-2" title="${$('<div/>').text(row.product_name_ar).html()}">
                                <span class="language-badge badge bg-success text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">AR</span>
                                <span class="product-name text-dark fw-semibold" dir="rtl" style="font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">${truncateStr(row.product_name_ar, 35)}</span>
                            </div>`;
                        }
                        
                        // Product Type (Bank/Regular)
                        const productType = row.product_type === 'bank' ? '{{ __("catalogmanagement::product.bank_product") }}' : '{{ __("catalogmanagement::product.regular_product") }}';
                        const typeClass = row.product_type === 'bank' ? 'bg-info' : 'bg-secondary';
                        html += `<div class="mb-2">
                            <span class="badge ${typeClass} text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 10px;">
                                <i class="uil ${row.product_type === 'bank' ? 'uil-database' : 'uil-box'} me-1"></i>
                                ${productType}
                            </span>
                        </div>`;
                        
                        // Configuration Type (Simple or Variant)
                        const configurationType = row.configuration_type || 'simple';
                        const configClass = configurationType === 'variants' ? 'bg-warning' : 'bg-success';
                        const configLabel = configurationType === 'variants' ? '{{ __("catalogmanagement::product.variant_product") }}' : '{{ __("catalogmanagement::product.simple_product") }}';
                        const configIcon = configurationType === 'variants' ? 'uil-layers' : 'uil-package';
                        html += `<div class="mb-2">
                            <span class="badge badge-round badge-lg ${configClass} text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 10px;">
                                <i class="uil ${configIcon} me-1"></i>
                                ${configLabel}
                            </span>
                        </div>`;
                        
                        // Meta information
                        html += '<div class="product-meta-info">';
                        if (row.department && row.department !== '-') {
                            html += `<div class="mb-1">
                                <small class="text-muted">{{ trans('catalogmanagement::product.department') }}:</small>
                                <span class="badge badge-secondary badge-round badge-lg ms-1">${$('<div/>').text(row.department).html()}</span>
                            </div>`;
                        }
                        if (row.category && row.category !== '-') {
                            html += `<div class="mb-1">
                                <small class="text-muted">{{ trans('catalogmanagement::product.category') }}:</small>
                                <span class="badge badge-secondary badge-round badge-lg ms-1">${$('<div/>').text(row.category).html()}</span>
                            </div>`;
                        }
                        if (row.brand && row.brand !== '-') {
                            html += `<div class="mb-1">
                                <small class="text-muted">{{ trans('catalogmanagement::product.brand') }}:</small>
                                <span class="badge badge-secondary badge-round badge-lg ms-1">${$('<div/>').text(row.brand).html()}</span>
                            </div>`;
                        }
                        
                        // Stock Information (Total Stock & Remaining Stock)
                        const totalStock = row.total_stock || 0;
                        const remainingStock = row.remaining_stock || 0;
                        const stockBadgeClass = remainingStock > 0 ? 'badge-success' : 'badge-danger';
                        html += `<div class="mb-1">
                            <small class="text-muted">{{ trans('catalogmanagement::product.total_stock') }}:</small>
                            <span class="badge badge-secondary badge-round badge-lg ms-1">${totalStock.toLocaleString()}</span>
                        </div>`;
                        html += `<div class="mb-1">
                            <small class="text-muted">{{ trans('catalogmanagement::product.remaining_stock') }}:</small>
                            <span class="badge ${stockBadgeClass} badge-round badge-lg ms-1">${remainingStock > 0 ? remainingStock.toLocaleString() : '{{ __('dashboard.out_of_stock') }}'}</span>
                        </div>`;
                        html += '</div>';
                        
                        html += '</div>';
                        return html;
                    },
                    className: 'text-start'
                },
                {
                    data: 'approval_status',
                    name: 'approval_status',
                    orderable: false,
                    className: 'text-center',
                    render: function(data) {
                        if (!data) {
                            return '<span class="badge badge-secondary badge-round badge-lg"><i class="uil uil-minus"></i> {{ trans('common.none') }}</span>';
                        }
                        if (data === 'approved') {
                            return '<span class="badge badge-success badge-round badge-lg"><i class="uil uil-check-circle"></i> {{ trans('common.approved') }}</span>';
                        } else if (data === 'rejected') {
                            return '<span class="badge badge-danger badge-round badge-lg"><i class="uil uil-times-circle"></i> {{ trans('common.rejected') }}</span>';
                        } else if (data === 'pending') {
                            return '<span class="badge badge-warning badge-round badge-lg"><i class="uil uil-clock"></i> {{ trans('common.pending') }}</span>';
                        }
                        return `<span class="badge badge-secondary badge-round badge-lg">${data}</span>`;
                    }
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    orderable: false,
                    className: 'text-center',
                    render: function(data) {
                        return data 
                            ? '<span class="badge badge-round badge-lg bg-success">{{ trans('common.active') }}</span>'
                            : '<span class="badge badge-round badge-lg bg-danger">{{ trans('common.inactive') }}</span>';
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
                zeroRecords: "{{ trans('catalogmanagement::product.no_products_found') }}",
                emptyTable: "{{ trans('catalogmanagement::product.no_products_found') }}",
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
                // Update products count badge
                $('#products-count').text(settings.json.recordsFiltered || 0);
            }
        });

        // Search handler with debounce
        let searchTimer;
        $('#products-search').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                productsTable.search($('#products-search').val()).draw();
            }, 500);
        });
    });
</script>
@endpush
