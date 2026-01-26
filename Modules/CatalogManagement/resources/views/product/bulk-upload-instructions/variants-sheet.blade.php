<div class="alert alert-light border-info mb-3" style="background-color: #e7f3ff; border-left: 4px solid #0d6efd;">
    <i class="uil uil-info-circle me-2 text-info"></i>
    <strong>{{ __('catalogmanagement::product.note') }}:</strong> {{ __('catalogmanagement::product.variants_sheet_note') }}
</div>
<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead class="table-light">
            <tr>
                <th style="width: 200px;">{{ __('catalogmanagement::product.column_name') }}</th>
                <th>{{ __('catalogmanagement::product.description') }}</th>
                <th style="width: 200px;">{{ __('catalogmanagement::product.where_to_get') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>product_sku</code></td>
                <td>{{ __('catalogmanagement::product.col_product_sku_desc') }} <strong>({{ __('catalogmanagement::product.required') }})</strong></td>
                <td>{{ __('catalogmanagement::product.col_product_sku_source') }}</td>
            </tr>
            <tr>
                <td><code>sku</code></td>
                <td>{{ __('catalogmanagement::product.col_variant_sku_desc') }} <strong>({{ __('catalogmanagement::product.required') }})</strong></td>
                <td>{{ __('catalogmanagement::product.col_variant_sku_source') }}</td>
            </tr>
            <tr>
                <td><code>variant_configuration_id</code></td>
                <td>{{ __('catalogmanagement::product.col_variant_config_desc') }}</td>
                <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
            </tr>
            <tr>
                <td><code>price</code></td>
                <td>{{ __('catalogmanagement::product.col_price_desc') }} <strong>({{ __('catalogmanagement::product.required') }})</strong></td>
                <td>{{ __('catalogmanagement::product.col_price_source') }}</td>
            </tr>
            <tr>
                <td><code>has_discount</code></td>
                <td>{{ __('catalogmanagement::product.col_has_discount_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_has_discount_source') }}</td>
            </tr>
            <tr>
                <td><code>price_before_discount</code></td>
                <td>{{ __('catalogmanagement::product.col_price_before_discount_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_price_before_discount_source') }}</td>
            </tr>
            <tr>
                <td><code>discount_end_date</code></td>
                <td>{{ __('catalogmanagement::product.col_discount_end_date_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_discount_end_date_source') }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="alert alert-warning mt-3">
    <i class="uil uil-exclamation-triangle me-2"></i>
    <strong>{{ __('catalogmanagement::product.important') }}:</strong> {{ __('catalogmanagement::product.variants_sku_note') }}
</div>