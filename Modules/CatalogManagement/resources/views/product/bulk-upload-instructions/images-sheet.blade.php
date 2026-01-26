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
                <td><code>sku</code></td>
                <td>{{ __('catalogmanagement::product.col_image_sku_desc') }} <strong>({{ __('catalogmanagement::product.required') }})</strong></td>
                <td>{{ __('catalogmanagement::product.col_image_sku_source') }}</td>
            </tr>
            <tr>
                <td><code>image</code></td>
                <td>{{ __('catalogmanagement::product.col_image_url_desc') }} <strong>({{ __('catalogmanagement::product.required') }})</strong></td>
                <td>{{ __('catalogmanagement::product.col_image_url_source') }}</td>
            </tr>
            <tr>
                <td><code>is_main</code></td>
                <td>{{ __('catalogmanagement::product.col_is_main_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_is_main_source') }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="alert alert-info mt-3">
    <i class="uil uil-info-circle me-2"></i>
    <strong>{{ __('catalogmanagement::product.note') }}:</strong> {{ __('catalogmanagement::product.images_sheet_note') }}
</div>