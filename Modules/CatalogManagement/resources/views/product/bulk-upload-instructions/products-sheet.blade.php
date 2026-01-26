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
                <td>{{ __('catalogmanagement::product.col_sku_desc') }} <strong>({{ __('catalogmanagement::product.required') }})</strong></td>
                <td>{{ __('catalogmanagement::product.col_sku_source') }}</td>
            </tr>
            @if(isAdmin())
            <tr>
                <td><code>vendor_id</code></td>
                <td>{{ __('catalogmanagement::product.col_vendor_id_desc') }} <strong>({{ __('catalogmanagement::product.required') }})</strong></td>
                <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
            </tr>
            @endif
            <tr>
                <td><code>title_en</code></td>
                <td>{{ __('catalogmanagement::product.col_title_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_title_source') }}</td>
            </tr>
            <tr>
                <td><code>title_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_title_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_title_source') }}</td>
            </tr>
            <tr>
                <td><code>description_en</code></td>
                <td>{{ __('catalogmanagement::product.col_description_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_description_source') }}</td>
            </tr>
            <tr>
                <td><code>description_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_description_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_description_source') }}</td>
            </tr>
            <tr>
                <td><code>summary_en</code></td>
                <td>{{ __('catalogmanagement::product.col_summary_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_summary_source') }}</td>
            </tr>
            <tr>
                <td><code>summary_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_summary_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_summary_source') }}</td>
            </tr>
            <tr>
                <td><code>features_en</code></td>
                <td>{{ __('catalogmanagement::product.col_features_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_features_source') }}</td>
            </tr>
            <tr>
                <td><code>features_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_features_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_features_source') }}</td>
            </tr>
            <tr>
                <td><code>instructions_en</code></td>
                <td>{{ __('catalogmanagement::product.col_instructions_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_instructions_source') }}</td>
            </tr>
            <tr>
                <td><code>instructions_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_instructions_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_instructions_source') }}</td>
            </tr>
            <tr>
                <td><code>extra_description_en</code></td>
                <td>{{ __('catalogmanagement::product.col_extra_description_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_extra_description_source') }}</td>
            </tr>
            <tr>
                <td><code>extra_description_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_extra_description_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_extra_description_source') }}</td>
            </tr>
            <tr>
                <td><code>material_en</code></td>
                <td>{{ __('catalogmanagement::product.col_material_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_material_source') }}</td>
            </tr>
            <tr>
                <td><code>material_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_material_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_material_source') }}</td>
            </tr>
            <tr>
                <td><code>tags_en</code></td>
                <td>{{ __('catalogmanagement::product.col_tags_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_tags_source') }}</td>
            </tr>
            <tr>
                <td><code>tags_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_tags_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_tags_source') }}</td>
            </tr>
            <tr>
                <td><code>meta_title_en</code></td>
                <td>{{ __('catalogmanagement::product.col_meta_title_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_meta_title_source') }}</td>
            </tr>
            <tr>
                <td><code>meta_title_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_meta_title_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_meta_title_source') }}</td>
            </tr>
            <tr>
                <td><code>meta_description_en</code></td>
                <td>{{ __('catalogmanagement::product.col_meta_description_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_meta_description_source') }}</td>
            </tr>
            <tr>
                <td><code>meta_description_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_meta_description_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_meta_description_source') }}</td>
            </tr>
            <tr>
                <td><code>meta_keywords_en</code></td>
                <td>{{ __('catalogmanagement::product.col_meta_keywords_en_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_meta_keywords_source') }}</td>
            </tr>
            <tr>
                <td><code>meta_keywords_ar</code></td>
                <td>{{ __('catalogmanagement::product.col_meta_keywords_ar_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_meta_keywords_source') }}</td>
            </tr>
            <tr>
                <td><code>department</code></td>
                <td>{{ __('catalogmanagement::product.col_department_desc') }}</td>
                <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
            </tr>
            <tr>
                <td><code>main_category</code></td>
                <td>{{ __('catalogmanagement::product.col_category_desc') }}</td>
                <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
            </tr>
            <tr>
                <td><code>sub_category</code></td>
                <td>{{ __('catalogmanagement::product.col_sub_category_desc') }}</td>
                <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
            </tr>
            <tr>
                <td><code>brand</code></td>
                <td>{{ __('catalogmanagement::product.col_brand_desc') }}</td>
                <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
            </tr>
            <tr>
                <td><code>have_varient</code></td>
                <td>{{ __('catalogmanagement::product.col_have_variant_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_have_variant_source') }}</td>
            </tr>
            <tr>
                <td><code>status</code></td>
                <td>{{ __('catalogmanagement::product.col_status_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_status_source') }}</td>
            </tr>
            <tr>
                <td><code>featured_product</code></td>
                <td>{{ __('catalogmanagement::product.col_featured_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_featured_source') }}</td>
            </tr>
            <tr>
                <td><code>max_per_order</code></td>
                <td>{{ __('catalogmanagement::product.col_max_per_order_desc') }}</td>
                <td>{{ __('catalogmanagement::product.col_max_per_order_source') }}</td>
            </tr>
        </tbody>
    </table>
</div>
