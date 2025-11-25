<script>
(function($) {
    'use strict';

    const config = {
        routes: {
            searchBankProducts: '{{ route("admin.products.bank.search") }}',
            getVendorProduct: '{{ route("admin.products.bank.vendor-product") }}',
            saveStock: '{{ route("admin.products.bank.save-stock") }}',
            variantKeys: '{{ route("admin.api.variant-keys") }}',
            variantsByKey: '{{ route("admin.api.variants-by-key") }}'
        },
        translations: {
            newVendorProduct: '{{ __("catalogmanagement::product.new_vendor_product") }}',
            existingVendorProduct: '{{ __("catalogmanagement::product.existing_vendor_product") }}',
            willCreateNew: '{{ __("catalogmanagement::product.will_create_new_vendor_product") }}',
            willEditExisting: '{{ __("catalogmanagement::product.will_edit_existing_vendor_product") }}',
            selectRegion: '{{ __("catalogmanagement::product.select_region") }}',
            selectOption: '{{ __("common.select_option") }}'
        }
    };

    let selectedProductId = null;
    let selectedVendorId = null;
    let vendorProductData = null;
    let variantCounter = 1000;
    let stockRowCounter = 0;
    let variantKeysData = [];
    let regionsData = [];

    $(document).ready(function() {
        initBankProductSelect();
        initVendorSelect();
        initEventHandlers();
        loadVariantKeys();
    });

    function initBankProductSelect() {
        $('#bank_product_select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '{{ __("catalogmanagement::product.search_bank_product") }}',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: config.routes.searchBankProducts,
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return { search: params.term, page: params.page || 1 };
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(function(product) {
                            return {
                                id: product.id,
                                text: product.title_en + (product.sku ? ' (' + product.sku + ')' : ''),
                                product: product
                            };
                        }),
                        pagination: { more: data.current_page < data.last_page }
                    };
                }
            }
        });

        $('#bank_product_select').on('select2:select', function(e) {
            const product = e.params.data.product;
            selectedProductId = product.id;
            showProductPreview(product);
            enableVendorStep();
        });

        $('#bank_product_select').on('select2:clear', function() {
            selectedProductId = null;
            hideProductPreview();
            disableVendorStep();
            hideStockManagement();
        });
    }

    function showProductPreview(product) {
        $('#preview-image').attr('src', product.image || '/images/placeholder.png');
        $('#preview-title-en').text(product.title_en || '-');
        $('#preview-title-ar').text(product.title_ar || '-');
        $('#preview-sku').text(product.sku || '-');
        $('#preview-brand').text(product.brand || '-');
        $('#preview-category').text(product.category || '-');
        $('#product-preview').show();
        $('#step-product').addClass('completed');
    }

    function hideProductPreview() {
        $('#product-preview').hide();
        $('#step-product').removeClass('completed');
    }

    function initVendorSelect() {
        $('#vendor_select').select2({ theme: 'bootstrap-5', width: '100%' });
        $('#vendor_select').on('change', function() {
            selectedVendorId = $(this).val();
            if (selectedVendorId && selectedProductId) {
                checkVendorProduct();
            } else {
                hideStockManagement();
            }
        });
    }

    function enableVendorStep() {
        $('#step-vendor').css({ opacity: 1, pointerEvents: 'auto' });
    }

    function disableVendorStep() {
        $('#step-vendor').css({ opacity: 0.5, pointerEvents: 'none' });
        $('#vendor_select').val(null).trigger('change');
        $('#vendor-product-status').hide();
        $('#step-vendor').removeClass('completed');
    }

    function checkVendorProduct() {
        // First load regions for the selected vendor
        loadVendorRegions(selectedVendorId, function() {
            // Then check for existing vendor product
            $.ajax({
                url: config.routes.getVendorProduct,
                type: 'GET',
                data: { product_id: selectedProductId, vendor_id: selectedVendorId },
                success: function(response) {
                    vendorProductData = response.vendor_product;

                    if (vendorProductData) {
                        $('#status-badge').removeClass('new').addClass('existing')
                            .html('<i class="uil uil-check-circle me-1"></i>' + config.translations.existingVendorProduct);
                        $('#status-message').text(config.translations.willEditExisting);
                        populateExistingData(vendorProductData);
                    } else {
                        $('#status-badge').removeClass('existing').addClass('new')
                            .html('<i class="uil uil-plus-circle me-1"></i>' + config.translations.newVendorProduct);
                        $('#status-message').text(config.translations.willCreateNew);
                        resetStockForm();
                    }

                    $('#vendor-product-status').show();
                    $('#step-vendor').addClass('completed');
                    showStockManagement();
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    if (typeof toastr !== 'undefined') toastr.error('{{ __("common.error") }}');
                }
            });
        });
    }

    function loadVendorRegions(vendorId, callback) {
        $.ajax({
            url: '{{ route("admin.area-settings.regions.datatable") }}',
            type: 'GET',
            data: { vendor_id: vendorId, length: 1000 },
            success: function(response) {
                regionsData = (response.data || []).map(function(region) {
                    return {
                        id: region.id,
                        name: region.name || region.name_en || '-'
                    };
                });
                if (callback) callback();
            },
            error: function(xhr) {
                console.error('Error loading regions:', xhr);
                regionsData = [];
                if (callback) callback();
            }
        });
    }

    function showStockManagement() {
        $('#form_product_id').val(selectedProductId);
        $('#form_vendor_id').val(selectedVendorId);
        $('#stock-management-section').show();
    }

    function hideStockManagement() {
        $('#stock-management-section').hide();
        vendorProductData = null;
    }

    function resetStockForm() {
        $('#form_vendor_product_id').val('');
        $('#configuration_type').val('').trigger('change');
        $('#tax_id').val('').trigger('change');
        $('#simple_sku').val('');
        $('#simple_price').val('');
        $('#simple_discount').prop('checked', false);
        $('#simple_discount_fields').hide();
        $('#simple-stock-rows').empty();
        $('#existing-variants-container').empty();
        $('#variants-container').empty();
        $('#simple-product-section').hide();
        $('#variants-section').hide();
        $('#variants-empty-state').show();
    }

    function populateExistingData(data) {
        $('#form_vendor_product_id').val(data.id);
        $('#configuration_type').val(data.configuration_type).trigger('change');
        $('#tax_id').val(data.tax_id).trigger('change');

        if (data.configuration_type === 'simple' && data.variants && data.variants.length > 0) {
            const variant = data.variants[0];
            $('#simple_sku').val(variant.sku || '');
            $('#simple_price').val(variant.price || '');
            $('#simple_discount').prop('checked', variant.has_discount);
            if (variant.has_discount) {
                $('#simple_discount_fields').show();
                $('input[name="price_before_discount"]').val(variant.price_before_discount || '');
                $('input[name="discount_end_date"]').val(variant.discount_end_date || '');
            }
            populateSimpleStocks(variant.stocks || []);
        } else if (data.configuration_type === 'variants' && data.variants) {
            populateExistingVariants(data.variants);
        }
    }

    function populateSimpleStocks(stocks) {
        $('#simple-stock-rows').empty();
        if (stocks.length === 0) {
            addSimpleStockRow();
        } else {
            stocks.forEach(function(stock, index) {
                addSimpleStockRow(stock.region_id, stock.quantity, stock.id);
            });
        }
        updateTotalStock('#simple-stock-table');
    }

    function populateExistingVariants(variants) {
        $('#existing-variants-container').empty();
        $('#variants-empty-state').hide();

        variants.forEach(function(variant, index) {
            const html = createExistingVariantHtml(variant, index);
            $('#existing-variants-container').append(html);
        });

        // Initialize Select2 for existing variants
        setTimeout(function() {
            $('#existing-variants-container .select2').select2({ theme: 'bootstrap-5', width: '100%' });
        }, 100);
    }

    function createExistingVariantHtml(variant, index) {
        let stockRowsHtml = '';
        (variant.stocks || []).forEach(function(stock, stockIndex) {
            stockRowsHtml += createStockRowHtml(`variants[${index}]`, stockIndex, stock.region_id, stock.quantity, stock.id);
        });
        if (stockRowsHtml === '') {
            stockRowsHtml = createStockRowHtml(`variants[${index}]`, 0);
        }

        return `
            <div class="card mb-3 existing-variant-box" data-variant-index="${index}">
                <div class="card-header">
                    <h6 class="mb-0"><i class="uil uil-layer-group me-2"></i>${variant.variant_name || 'Variant #' + (index + 1)}</h6>
                </div>
                <div class="card-body">
                    <input type="hidden" name="variants[${index}][id]" value="${variant.id}">
                    <input type="hidden" name="variants[${index}][variant_configuration_id]" value="${variant.variant_configuration_id || ''}">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('catalogmanagement::product.sku') }}</label>
                            <input type="text" name="variants[${index}][sku]" class="form-control" value="${variant.sku || ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('catalogmanagement::product.price') }}</label>
                            <input type="number" name="variants[${index}][price]" class="form-control" step="0.01" min="0" value="${variant.price || ''}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-0">{{ __('catalogmanagement::product.enable_discount_offer') }}</label>
                        <div class="form-check form-switch form-switch-lg">
                            <input type="hidden" name="variants[${index}][has_discount]" value="0">
                            <input type="checkbox" name="variants[${index}][has_discount]" class="form-check-input variant-discount-switch" value="1" ${variant.has_discount ? 'checked' : ''}>
                        </div>
                        <div class="variant-discount-fields mt-3" style="display: ${variant.has_discount ? 'block' : 'none'};">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('catalogmanagement::product.price_before_discount') }}</label>
                                    <input type="number" name="variants[${index}][price_before_discount]" class="form-control" step="0.01" min="0" value="${variant.price_before_discount || ''}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('catalogmanagement::product.discount_end_date') }}</label>
                                    <input type="date" name="variants[${index}][discount_end_date]" class="form-control" value="${variant.discount_end_date || ''}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('catalogmanagement::product.stock_per_region') }}</label>
                        <div class="table-responsive">
                            <table class="table table-bordered variant-stock-table">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="width: 50%;">{{ __('catalogmanagement::product.region') }}</th>
                                        <th style="width: 35%;">{{ __('catalogmanagement::product.quantity') }}</th>
                                        <th style="width: 15%; text-align: center;">{{ __('catalogmanagement::product.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="variant-stock-rows">${stockRowsHtml}</tbody>
                                <tfoot>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="text-end"><strong>{{ __('catalogmanagement::product.total_stock') }}:</strong></td>
                                        <td><span class="badge badge-primary total-stock-display">0</span></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm mt-2 add-existing-variant-stock-row" data-variant-index="${index}">
                            <i class="uil uil-plus me-1"></i> {{ __('catalogmanagement::product.add_region') }}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    function createStockRowHtml(namePrefix, stockIndex, regionId = '', quantity = 0, stockId = '') {
        let regionOptions = `<option value="">${config.translations.selectRegion}</option>`;
        regionsData.forEach(function(region) {
            regionOptions += `<option value="${region.id}" ${region.id == regionId ? 'selected' : ''}>${region.name}</option>`;
        });

        return `
            <tr class="stock-row">
                ${stockId ? `<input type="hidden" name="${namePrefix}[stocks][${stockIndex}][id]" value="${stockId}">` : ''}
                <td>
                    <select name="${namePrefix}[stocks][${stockIndex}][region_id]" class="form-control select2 region-select">${regionOptions}</select>
                </td>
                <td>
                    <input type="number" name="${namePrefix}[stocks][${stockIndex}][quantity]" class="form-control quantity-input" value="${quantity}" min="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-stock-row"><i class="uil uil-trash-alt m-0"></i></button>
                </td>
            </tr>
        `;
    }

    function addSimpleStockRow(regionId = '', quantity = 0, stockId = '') {
        const rowCount = $('#simple-stock-rows tr').length;
        const html = createStockRowHtml('', rowCount, regionId, quantity, stockId)
            .replace(/name=""/g, `name="stocks[${rowCount}]"`)
            .replace(/\[stocks\]\[/g, '[');

        $('#simple-stock-rows').append(html);
        setTimeout(function() {
            $('#simple-stock-rows tr:last .select2').select2({ theme: 'bootstrap-5', width: '100%' });
        }, 50);
    }

    function updateTotalStock(tableSelector) {
        let total = 0;
        $(tableSelector).find('.quantity-input').each(function() {
            total += parseInt($(this).val()) || 0;
        });
        $(tableSelector).find('.total-stock-display').text(total);
    }

    function loadVariantKeys() {
        $.ajax({
            url: config.routes.variantKeys,
            type: 'GET',
            success: function(response) {
                variantKeysData = response.data || response;
            }
        });
    }

    function initEventHandlers() {
        // Configuration type change
        $('#configuration_type').on('change', function() {
            const type = $(this).val();
            $('#simple-product-section').toggle(type === 'simple');
            $('#variants-section').toggle(type === 'variants');

            if (type === 'simple' && $('#simple-stock-rows tr').length === 0) {
                addSimpleStockRow();
            }
        });

        // Simple discount toggle
        $('#simple_discount').on('change', function() {
            $('#simple_discount_fields').toggle($(this).is(':checked'));
        });

        // Add simple stock row
        $('#add-simple-stock-row').on('click', function() {
            addSimpleStockRow();
        });

        // Remove stock row
        $(document).on('click', '.remove-stock-row', function() {
            const $table = $(this).closest('table');
            if ($table.find('.stock-row').length > 1) {
                $(this).closest('tr').remove();
                updateTotalStock($table);
            }
        });

        // Update total on quantity change
        $(document).on('input', '.quantity-input', function() {
            updateTotalStock($(this).closest('table'));
        });

        // Variant discount toggle
        $(document).on('change', '.variant-discount-switch', function() {
            $(this).closest('.card-body').find('.variant-discount-fields').toggle($(this).is(':checked'));
        });

        // Add variant stock row for existing variants
        $(document).on('click', '.add-existing-variant-stock-row', function() {
            const variantIndex = $(this).data('variant-index');
            const $tbody = $(this).closest('.card-body').find('.variant-stock-rows');
            const rowCount = $tbody.find('tr').length;
            const html = createStockRowHtml(`variants[${variantIndex}]`, rowCount);
            $tbody.append(html);
            setTimeout(function() {
                $tbody.find('tr:last .select2').select2({ theme: 'bootstrap-5', width: '100%' });
            }, 50);
        });

        // Add new variant
        $('#add-variant-btn').on('click', function() {
            addNewVariant();
        });

        // Remove variant
        $(document).on('click', '.remove-variant-btn', function() {
            $(this).closest('.variant-box').remove();
            if ($('#variants-container .variant-box').length === 0 && $('#existing-variants-container .existing-variant-box').length === 0) {
                $('#variants-empty-state').show();
            }
        });

        // Variant key selection
        $(document).on('change', '.variant-key-select', function() {
            const keyId = $(this).val();
            const variantIndex = $(this).closest('.variant-box').data('variant-index');
            if (keyId) {
                loadVariantsByKey(variantIndex, keyId);
            } else {
                $(`#variant-${variantIndex} .variant-tree-container`).hide();
                $(`#variant-${variantIndex}-pricing-stock`).hide();
            }
        });

        // Form submission
        $('#stockForm').on('submit', function(e) {
            e.preventDefault();
            submitForm();
        });
    }

    function addNewVariant() {
        const template = $('#variant-box-template').html();
        const html = template
            .replace(/__VARIANT_INDEX__/g, variantCounter)
            .replace(/__VARIANT_NUMBER__/g, variantCounter + 1);

        $('#variants-container').append(html);
        $('#variants-empty-state').hide();

        const $keySelect = $(`#variant-${variantCounter} .variant-key-select`);
        variantKeysData.forEach(function(key) {
            $keySelect.append(`<option value="${key.id}">${key.name}</option>`);
        });

        setTimeout(function() {
            $keySelect.select2({ theme: 'bootstrap-5', width: '100%' });
        }, 50);

        variantCounter++;
    }

    function loadVariantsByKey(variantIndex, keyId) {
        const $container = $(`#variant-${variantIndex} .variant-tree-container`);
        const $levels = $(`#variant-${variantIndex} .variant-tree-levels`);

        $levels.empty();
        $container.hide();
        $(`#variant-${variantIndex}-pricing-stock`).hide();
        $(`#variant-${variantIndex} .selected-variant-path`).hide();
        $(`#variant-${variantIndex}`).data('current-key-id', keyId);

        $.ajax({
            url: config.routes.variantsByKey,
            type: 'GET',
            data: { key_id: keyId },
            success: function(response) {
                const variants = response.data || response;
                if (variants.length > 0) {
                    $container.show();
                    addVariantLevel($levels, variants, variantIndex, 0, []);
                }
            }
        });
    }

    function addVariantLevel($container, variants, variantIndex, level, selectedPath) {
        const levelDiv = $('<div>', { class: 'variant-level mb-3', 'data-level': level });
        const select = $('<select>', {
            class: 'form-control select2 variant-value-select',
            'data-variant-index': variantIndex,
            'data-level': level
        });

        select.append(`<option value="">${config.translations.selectOption}</option>`);
        variants.forEach(function(variant) {
            const hasChildren = variant.has_children || false;
            select.append(`<option value="${variant.id}" data-has-children="${hasChildren}">${variant.name}${hasChildren ? ' 🌳' : ''}</option>`);
        });

        levelDiv.append(select);
        $container.append(levelDiv);

        setTimeout(function() {
            select.select2({ theme: 'bootstrap-5', width: '100%' });
        }, 50);
    }

    $(document).on('change', '.variant-value-select', function() {
        const $select = $(this);
        const variantId = $select.val();
        const variantIndex = $select.data('variant-index');
        const level = $select.data('level');
        const keyId = $(`#variant-${variantIndex}`).data('current-key-id');
        const $levels = $(`#variant-${variantIndex} .variant-tree-levels`);

        $levels.find('.variant-level').each(function() {
            if (parseInt($(this).data('level')) > level) $(this).remove();
        });

        $(`#variant-${variantIndex}-pricing-stock`).hide();
        $(`#variant-${variantIndex} .selected-variant-path`).hide();

        if (!variantId) return;

        const selectedPath = [];
        $(`#variant-${variantIndex} .variant-value-select`).each(function(i) {
            if (i <= level && $(this).val()) {
                selectedPath.push($(this).find('option:selected').text().replace(' 🌳', ''));
            }
        });

        const hasChildren = $select.find('option:selected').data('has-children');

        if (hasChildren) {
            $.ajax({
                url: config.routes.variantsByKey,
                type: 'GET',
                data: { key_id: keyId, parent_id: variantId },
                success: function(response) {
                    const variants = response.data || response;
                    if (variants.length > 0) {
                        addVariantLevel($levels, variants, variantIndex, level + 1, selectedPath);
                    } else {
                        finalizeVariantSelection(variantIndex, variantId, selectedPath);
                    }
                }
            });
        } else {
            finalizeVariantSelection(variantIndex, variantId, selectedPath);
        }
    });

    function finalizeVariantSelection(variantIndex, variantId, path) {
        $(`#variant-${variantIndex} .selected-variant-id`).val(variantId);
        $(`#variant-${variantIndex} .selected-variant-path .path-text`).text(path.join(' → '));
        $(`#variant-${variantIndex} .selected-variant-path`).show();

        const $pricingStock = $(`#variant-${variantIndex}-pricing-stock`);
        $pricingStock.show();

        // Add initial stock row if empty
        const $stockRows = $(`#variant-${variantIndex}-stock-rows`);
        if ($stockRows.find('tr').length === 0) {
            const html = createStockRowHtml(`variants[${variantIndex}]`, 0);
            $stockRows.append(html);
            setTimeout(function() {
                $stockRows.find('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
            }, 50);
        }
    }

    // Add variant stock row for new variants
    $(document).on('click', '.add-variant-stock-row', function() {
        const variantIndex = $(this).data('variant-index');
        const $tbody = $(`#variant-${variantIndex}-stock-rows`);
        const rowCount = $tbody.find('tr').length;
        const html = createStockRowHtml(`variants[${variantIndex}]`, rowCount);
        $tbody.append(html);
        setTimeout(function() {
            $tbody.find('tr:last .select2').select2({ theme: 'bootstrap-5', width: '100%' });
        }, 50);
    });

    function submitForm() {
        const $btn = $('#submitBtn');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>{{ __("common.processing") }}');

        if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.show();

        $.ajax({
            url: config.routes.saveStock,
            type: 'POST',
            data: $('#stockForm').serialize(),
            success: function(response) {
                if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();

                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    }
                    setTimeout(function() {
                        window.location.href = '{{ route("admin.products.bank") }}';
                    }, 1500);
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(response.message);
                    $btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr) {
                if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                $btn.prop('disabled', false).html(originalHtml);

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    Object.values(xhr.responseJSON.errors).forEach(function(errors) {
                        if (typeof toastr !== 'undefined') toastr.error(errors[0]);
                    });
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('{{ __("common.error") }}');
                }
            }
        });
    }

})(jQuery);
</script>
