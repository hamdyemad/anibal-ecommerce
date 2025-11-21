/**
 * Product Form Edit Mode Module
 * Handles edit mode functionality, data population, and cascading selects
 */

class ProductFormEdit {
    constructor() {
        this.config = window.productFormConfig || {};
    }

    /**
     * Load departments for edit mode
     */
    loadDepartmentsForEdit(vendorId, selectedDepartmentId) {
        console.log("🔧 Loading departments for edit mode, vendor:", vendorId);

        const departmentsRoute = this.config.departmentsRoute || '/api/departments';
        const url = `${departmentsRoute}?vendor_id=${vendorId}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ Departments fetched:', data);
            this.populateDepartmentSelect(data, selectedDepartmentId);
        })
        .catch(error => {
            console.error('❌ Error fetching departments:', error);
            this.handleDepartmentError();
        });
    }

    /**
     * Populate department select with data
     */
    populateDepartmentSelect(data, selectedDepartmentId) {
        const departmentSelect = $('#department_id');
        departmentSelect.empty().append('<option value="">Select Department</option>');

        if (data && Array.isArray(data) && data.length > 0) {
            data.forEach(dept => {
                departmentSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
            });
            console.log(`✅ Loaded ${data.length} departments`);
        } else if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
            data.data.forEach(dept => {
                departmentSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
            });
            console.log(`✅ Loaded ${data.data.length} departments`);
        } else {
            console.log('⚠️ No departments found');
            departmentSelect.append('<option value="">No departments available</option>');
        }

        this.refreshSelect2(departmentSelect, selectedDepartmentId);
    }

    /**
     * Handle department loading error
     */
    handleDepartmentError() {
        const departmentSelect = $('#department_id');
        departmentSelect.empty().append('<option value="">Error loading departments</option>');
        this.refreshSelect2(departmentSelect);
    }

    /**
     * Refresh Select2 and set selected value
     */
    refreshSelect2(selectElement, selectedValue = null) {
        // Check if Select2 is available
        if (typeof $.fn.select2 === 'undefined') {
            console.warn('⚠️ Select2 not available, skipping refresh');
            return;
        }

        if (selectElement.data('select2')) {
            selectElement.select2('destroy');
        }

        selectElement.select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: false
        });

        if (selectedValue && this.config.isEditMode) {
            setTimeout(() => {
                selectElement.val(selectedValue);
                selectElement.trigger('change.select2');
                console.log('🔄 Set selected value:', selectedValue);
            }, 100);
        }
    }

    /**
     * Load categories for edit mode
     */
    loadCategoriesForEdit(departmentId, selectedCategoryId) {
        console.log("🔧 Loading categories for edit mode, department:", departmentId);

        const categoriesRoute = this.config.categoriesRoute || '/api/categories';
        const url = `${categoriesRoute}?department_id=${departmentId}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(response => {
            const categorySelect = $("#category_id");
            categorySelect.empty().append('<option value="">Select Category</option>');

            if (response.status && response.data && response.data.length > 0) {
                response.data.forEach(category => {
                    categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                });
                categorySelect.val(selectedCategoryId);
                console.log("🔧 Categories loaded and selected for edit mode");
            }
        })
        .catch(error => console.error("❌ Error loading categories for edit:", error));
    }

    /**
     * Load subcategories for edit mode
     */
    loadSubCategoriesForEdit(categoryId, selectedSubCategoryId) {
        console.log("🔧 Loading subcategories for edit mode, category:", categoryId);

        const subCategoriesRoute = this.config.subCategoriesRoute || '/api/sub-categories';
        const url = `${subCategoriesRoute}?category_id=${categoryId}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(response => {
            const subCategorySelect = $("#sub_category_id");
            subCategorySelect.empty().append('<option value="">Select Sub Category</option>');

            if (response.status && response.data && response.data.length > 0) {
                response.data.forEach(subcategory => {
                    subCategorySelect.append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                });
                subCategorySelect.val(selectedSubCategoryId);
                console.log("🔧 Subcategories loaded and selected for edit mode");
            }
        })
        .catch(error => console.error("❌ Error loading subcategories for edit:", error));
    }

    /**
     * Populate product details for edit mode
     */
    populateProductDetailsForEdit() {
        const selectedValues = this.config.selectedValues;

        if (!selectedValues || !this.config.isEditMode) {
            return;
        }

        console.log('🔧 Populating product details for edit mode');

        if (selectedValues.configuration_type === 'simple') {
            this.populateSimpleProductFields();
        } else if (selectedValues.configuration_type === 'variants' && selectedValues.existingVariants?.length > 0) {
            this.populateExistingVariants();
        }
    }

    /**
     * Populate simple product fields
     */
    populateSimpleProductFields() {
        const selectedValues = this.config.selectedValues;

        console.log('🔧 Populating simple product fields');
        console.log('🔧 Product SKU from config:', selectedValues?.productSku);
        console.log('🔧 Product Price from config:', selectedValues?.productPrice);

        // Set SKU
        if (selectedValues?.productSku) {
            $('#sku').val(selectedValues.productSku);
            console.log('🔧 Set SKU:', selectedValues.productSku);
        }

        // Set Price
        if (selectedValues?.productPrice) {
            $('#price').val(selectedValues.productPrice);
            console.log('🔧 Set Price:', selectedValues.productPrice);
        }

        // Set Discount
        if (selectedValues?.productHasDiscount) {
            $('.simple-discount-toggle').prop('checked', true).trigger('change');

            setTimeout(() => {
                if (selectedValues?.productPriceBeforeDiscount) {
                    $('input[name="price_before_discount"]').val(selectedValues.productPriceBeforeDiscount);
                }

                if (selectedValues?.productOfferEndDate) {
                    $('input[name="offer_end_date"]').val(selectedValues.productOfferEndDate);
                }
            }, 100);

            console.log('🔧 Set Discount fields');
        }

        // Populate stock data
        this.populateSimpleProductStocks();
    }

    /**
     * Populate stock data for simple products
     */
    populateSimpleProductStocks() {
        const existingVariants = this.config.selectedValues?.existingVariants || [];

        console.log('🔧 Populating simple product stocks, variants:', existingVariants);

        if (existingVariants.length > 0 && existingVariants[0].stocks && existingVariants[0].stocks.length > 0) {
            const stocks = existingVariants[0].stocks;
            console.log('🔧 Found stocks to populate:', stocks);

            setTimeout(() => {
                stocks.forEach((stock, index) => {
                    setTimeout(() => {
                        this.addStockRow(stock, index);
                    }, 400 * index);
                });
            }, 500);
        } else {
            console.log('⚠️ No stocks found for simple product');
        }
    }

    /**
     * Add and populate a stock row
     */
    addStockRow(stock, index) {
        // Find and click add stock button
        let addButton = $('.add-stock-row').first();
        if (!addButton.length) {
            addButton = $('button:contains("Add New Region")').first();
        }

        if (addButton.length) {
            addButton.trigger('click');

            setTimeout(() => {
                const stockRows = $('.stock-row, [class*="stock-row"]');
                const currentRow = stockRows.eq(index);

                if (currentRow.length) {
                    const regionSelect = currentRow.find('select[name*="region"]').first();
                    const quantityInput = currentRow.find('input[name*="quantity"]').first();

                    if (regionSelect.length && quantityInput.length) {
                        regionSelect.val(stock.region_id);
                        quantityInput.val(stock.quantity);

                        regionSelect.trigger('change');
                        quantityInput.trigger('input');

                        console.log(`✅ Populated stock row ${index + 1}: Region ${stock.region_id}, Quantity ${stock.quantity}`);
                    }
                }
            }, 300);
        }
    }

    /**
     * Populate existing variants
     */
    populateExistingVariants() {
        const existingVariants = this.config.selectedValues?.existingVariants || [];

        console.log('🔧 Populating existing variants:', existingVariants.length);

        existingVariants.forEach((variant, index) => {
            window.ProductFormVariants?.addVariantBox();

            setTimeout(() => {
                this.populateVariantFields(variant, index + 1);
            }, 100 * (index + 1));
        });
    }

    /**
     * Populate individual variant fields
     */
    populateVariantFields(variant, variantIndex) {
        // Set SKU
        if (variant.sku) {
            $(`input[name="variants[${variantIndex}][sku]"]`).val(variant.sku);
        }

        // Set Price
        if (variant.price) {
            $(`input[name="variants[${variantIndex}][price]"]`).val(variant.price);
        }

        // Set Discount
        if (variant.has_discount) {
            $(`.variant-discount-toggle[name="variants[${variantIndex}][has_discount]"]`).prop('checked', true).trigger('change');

            if (variant.price_before_discount) {
                $(`input[name="variants[${variantIndex}][price_before_discount]"]`).val(variant.price_before_discount);
            }

            if (variant.offer_end_date) {
                $(`input[name="variants[${variantIndex}][offer_end_date]"]`).val(variant.offer_end_date);
            }
        }

        // Populate stock data for this variant
        if (variant.stocks && variant.stocks.length > 0) {
            variant.stocks.forEach((stock, stockIndex) => {
                setTimeout(() => {
                    this.addVariantStockRow(variantIndex, stock, stockIndex);
                }, 100 * stockIndex);
            });
        }

        console.log(`🔧 Populated variant ${variantIndex}:`, variant.sku);
    }

    /**
     * Add and populate variant stock row
     */
    addVariantStockRow(variantIndex, stock, stockIndex) {
        const addButton = $(`.add-stock-row-variant[data-variant-index="${variantIndex}"]`);

        if (addButton.length) {
            addButton.trigger('click');

            setTimeout(() => {
                const stockRows = $(`.variant-stock-row[data-variant-index="${variantIndex}"]`);
                const currentRow = stockRows.eq(stockIndex);

                if (currentRow.length) {
                    currentRow.find('select[name*="[region_id]"]').val(stock.region_id);
                    currentRow.find('input[name*="[quantity]"]').val(stock.quantity);

                    console.log(`🔧 Populated variant ${variantIndex} stock row ${stockIndex + 1}:`, stock);
                }
            }, 100);
        }
    }
}

// Export for global use
window.ProductFormEdit = ProductFormEdit;
