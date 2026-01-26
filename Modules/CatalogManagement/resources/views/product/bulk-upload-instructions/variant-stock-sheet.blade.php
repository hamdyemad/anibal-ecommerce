<div class="alert alert-light border-info mb-3" style="background-color: #e7f3ff; border-left: 4px solid #0d6efd;">
    <i class="uil uil-info-circle me-2 text-info"></i>
    <strong>{{ __('catalogmanagement::product.note') }}:</strong> {{ __('catalogmanagement::product.stock_sheet_note') }}
</div>
<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead class="table-light">
            <tr>
                <th style="width: 150px;">{{ __('catalogmanagement::product.column_name') }}</th>
                <th>{{ __('catalogmanagement::product.description') }}</th>
                <th style="width: 200px;">{{ __('catalogmanagement::product.where_to_get') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>variant_sku</code></td>
                <td>{{ __('catalogmanagement::product.col_stock_variant_sku_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_stock_variant_sku_source') }}</td>
            </tr>
            <tr>
                <td><code>region_id</code></td>
                <td>{{ __('catalogmanagement::product.col_region_id_desc') }}</td>
                <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
            </tr>
            <tr>
                <td><code>stock</code></td>
                <td>{{ __('catalogmanagement::product.col_stock_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_stock_source') }}</td>
            </tr>
        </tbody>
    </table>
</div>
