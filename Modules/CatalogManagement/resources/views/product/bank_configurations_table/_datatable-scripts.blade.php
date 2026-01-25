{{-- Bank Products DataTable Render Functions --}}
<script>
// Render product information column
window.renderBankProductInformation = function(data, type, row) {
    if (!data) return '<span class="text-muted">—</span>';
    let html = '<div class="product-info-container">';

    // Product Names
    if (data.name_en && data.name_en !== '-') {
        html += `<div class="product-name-item mb-2">
            <span class="language-badge badge bg-primary text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">EN</span>
            <span class="product-name text-dark fw-semibold">${$('<div/>').text(data.name_en).html()}</span>
        </div>`;
    }

    if (data.name_ar && data.name_ar !== '-') {
        html += `<div class="product-name-item mb-2">
            <span class="language-badge badge bg-success text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">AR</span>
            <span class="product-name text-dark fw-semibold" dir="rtl" style="font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">${$('<div/>').text(data.name_ar).html()}</span>
        </div>`;
    }

    // Additional Information
    html += '<div class="product-meta mt-2">';
    
    // Brand
    if (row.brand && row.brand.name) {
        html += `<div class="mb-1">
            <span class="text-muted me-1" style="font-size: 11px;">{{ __('catalogmanagement::product.brand') }}:</span>
            <span class="badge badge-lg badge-round bg-info text-white px-2 py-1" style="font-size: 11px;">
                <i class="uil uil-tag-alt me-1"></i>${$('<div/>').text(row.brand.name).html()}
            </span>
        </div>`;
    }
    
    // Department
    if (row.department && row.department.name) {
        html += `<div class="mb-1">
            <span class="text-muted me-1" style="font-size: 11px;">{{ __('catalogmanagement::product.department') }}:</span>
            <span class="badge badge-lg badge-round bg-secondary text-white px-2 py-1" style="font-size: 11px;">
                <i class="uil uil-building me-1"></i>${$('<div/>').text(row.department.name).html()}
            </span>
        </div>`;
    }

    // Category
    if (row.category && row.category.name) {
        html += `<div class="mb-1">
            <span class="text-muted me-1" style="font-size: 11px;">{{ __('catalogmanagement::product.category') }}:</span>
            <span class="badge badge-lg badge-round bg-secondary text-white px-2 py-1" style="font-size: 11px;">
                <i class="uil uil-folder me-1"></i>${$('<div/>').text(row.category.name).html()}
            </span>
        </div>`;
    }
    
    // Sub-Category
    if (row.sub_category && row.sub_category.name) {
        html += `<div class="mb-1">
            <span class="text-muted me-1" style="font-size: 11px;">{{ __('catalogmanagement::product.sub_category') }}:</span>
            <span class="badge badge-lg badge-round bg-secondary px-2 py-1" style="font-size: 11px;">
                <i class="uil uil-folder-open me-1"></i>${$('<div/>').text(row.sub_category.name).html()}
            </span>
        </div>`;
    }
    
    html += '</div>';
    html += '</div>';
    return html;
};

// Render activation column
window.renderBankActivation = function(data, type, row) {
    // For sorting, return numeric value
    if (type === 'sort' || type === 'type') {
        return data ? 1 : 0;
    }

    @can('products.bank.change-activation')
        // For display with switcher
        const isChecked = data ? 'checked' : '';
        const switchId = 'activation-switch-' + row.id;
        const productName = row.product_information?.name_en || row.product_information?.name_ar || 'Product #' + row.id;

        return `<div class="userDatatable-content">
            <div class="form-switch">
                <input class="form-check-input activation-switcher"
                       type="checkbox"
                       id="${switchId}"
                       data-product-id="${row.id}"
                       data-product-name="${$('<div>').text(productName).html()}"
                       ${isChecked}
                       style="cursor: pointer;">
                <label class="form-check-label" for="${switchId}"></label>
            </div>
        </div>`;
    @else
        // For display without switcher
        return data 
            ? '<span class="badge badge-round bg-success">{{ trans("common.active") }}</span>'
            : '<span class="badge badge-round bg-danger">{{ trans("common.inactive") }}</span>';
    @endcan
};

// Render actions column
window.renderBankActions = function(data, type, row) {
    const bankViewUrl = "{{ route('admin.products.bank.view', ':id') }}".replace(':id', row.id);
    
    let actions = `<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
        <a href="${bankViewUrl}" class="view btn btn-info table_action_father" title="{{ trans('catalogmanagement::product.view_bank_product') }}">
            <i class="uil uil-eye table_action_icon"></i>
        </a>
    </div>`;
    
    return actions;
};
</script>
