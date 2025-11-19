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

        // Set SKU (for simple product in Step 3, not the basic SKU in Step 1)
        if (selectedValues?.productSku) {
            // Target the simple product SKU field (input[name="sku"] but not #sku from Step 1)
            const simpleSkuField = $('input[name="sku"]').not('#sku');
            if (simpleSkuField.length) {
                simpleSkuField.val(selectedValues.productSku);
                console.log('🔧 Set simple product SKU field:', selectedValues.productSku);
            } else {
                console.warn('⚠️ Simple product SKU field not found');
            }
        }

        // Set Price
        if (selectedValues?.productPrice) {
            const priceField = $('input[name="price"]');
            if (priceField.length) {
                priceField.val(selectedValues.productPrice);
                console.log('🔧 Set Price:', selectedValues.productPrice);
            } else {
                console.warn('⚠️ Price field not found');
            }
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
    async populateSimpleProductStocks() {
        const existingVariants = this.config.selectedValues?.existingVariants || [];

        console.log('🔧 Populating simple product stocks');
        console.log('🔧 Existing variants:', existingVariants);

        if (existingVariants.length > 0 && existingVariants[0].stocks && existingVariants[0].stocks.length > 0) {
            const stocks = existingVariants[0].stocks;
            console.log('🔧 Found ' + stocks.length + ' stock(s) to populate:', stocks);

            // Log each stock for debugging
            stocks.forEach((stock, idx) => {
                console.log(`📦 Stock ${idx + 1}:`, {
                    region_id: stock.region_id,
                    region_name: stock.region_name,
                    quantity: stock.quantity
                });
            });

            // Add and populate stock rows sequentially using async/await
            for (let index = 0; index < stocks.length; index++) {
                const stock = stocks[index];
                console.log(`🔄 Adding and populating stock row ${index + 1}...`);
                await this.addAndPopulateStockRow(stock, index);
            }

            console.log('✅ All stock rows populated successfully');
        } else {
            console.log('⚠️ No stocks found for simple product');
            console.log('⚠️ Variants length:', existingVariants.length);
            if (existingVariants.length > 0) {
                console.log('⚠️ First variant:', existingVariants[0]);
                console.log('⚠️ Stocks:', existingVariants[0].stocks);
            }
        }
    }

    /**
     * Add and populate a stock row using Promise-based approach
     */
    async addAndPopulateStockRow(stock, index) {
        console.log(`🔄 Adding stock row ${index + 1} with data:`, stock);

        // Find the stock container
        const container = $('#simple-product-section').length ? $('#simple-product-section') : $('.card:has(.stock-rows-container)');

        if (!container.length) {
            console.warn('⚠️ Stock container not found');
            return;
        }

        // Get the variants module instance from the main productForm
        const variantsModule = window.productForm?.getModule('variants');
        if (!variantsModule) {
            console.warn('⚠️ ProductFormVariants module not available');
            return;
        }

        try {
            // Add stock row and get the row elements
            const rowData = await variantsModule.addStockRow(container, false, null);

            console.log('✅ Stock row created, received:', rowData);

            if (rowData && rowData.regionSelect && rowData.quantityInput) {
                const { regionSelect, quantityInput } = rowData;

                console.log(`📝 Setting region_id to: ${stock.region_id}`);
                console.log(`📝 Setting quantity to: ${stock.quantity}`);

                // Set values using Select2 API
                regionSelect.val(stock.region_id).trigger('change');
                quantityInput.val(stock.quantity).trigger('input');

                console.log('🔍 Region select value after set:', regionSelect.val());
                console.log('🔍 Quantity input value after set:', quantityInput.val());

                console.log(`✅ Populated stock row ${index + 1}: Region ${stock.region_id} (${stock.region_name}), Quantity ${stock.quantity}`);
            } else {
                console.warn('⚠️ Row elements not returned properly');
            }
        } catch (error) {
            console.error(`❌ Error adding stock row ${index + 1}:`, error);
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
