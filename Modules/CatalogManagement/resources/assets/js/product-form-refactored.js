/**
 * Product Form - Main Entry Point (Refactored)
 * This is the new, modular version of the product form
 *
 * Modules:
 * - FormInit: Initialization and configuration
 * - FormEdit: Edit mode functionality
 * - FormVariants: Variant and stock management
 * - FormValidation: Form validation and submission
 */

class ProductForm {
    constructor() {
        this.modules = {};
        this.isInitialized = false;
    }

    /**
     * Initialize the product form
     */
    async init() {
        if (this.isInitialized) {
            console.warn('⚠️ Product form already initialized');
            return;
        }

        console.log('🚀 Starting Product Form initialization...');

        try {
            // Initialize modules
            await this.initializeModules();

            // Setup global event handlers
            this.setupGlobalEventHandlers();

            // Setup wizard navigation
            this.setupWizardNavigation();

            // Setup cascading selects
            this.setupCascadingSelects();

            this.isInitialized = true;
            console.log('✅ Product Form fully initialized');

        } catch (error) {
            console.error('❌ Failed to initialize Product Form:', error);
        }
    }

    /**
     * Initialize all modules
     */
    async initializeModules() {
        console.log('📦 Initializing modules...');

        // Initialize core modules
        this.modules.init = new ProductFormInit();
        this.modules.edit = new ProductFormEdit();
        this.modules.variants = new ProductFormVariants();
        this.modules.validation = new ProductFormValidation();
        this.modules.wizard = new ProductFormWizard();

        // Initialize each module
        console.log('🔧 Initializing init module...');
        this.modules.init.init();

        console.log('🔧 Initializing variants module...');
        this.modules.variants.init();

        console.log('🔧 Initializing validation module...');
        this.modules.validation.init();

        console.log('🔧 Initializing wizard module...');
        this.modules.wizard.init();

        console.log('✅ All modules initialized');
        console.log('📋 Available modules:', Object.keys(this.modules));
    }

    /**
     * Setup global event handlers
     */
    setupGlobalEventHandlers() {
        // Stock management
        $(document).on('click', '.add-stock-row', (e) => {
            const container = $(e.target).closest('.card');
            this.modules.variants.addStockRow(container, false);
        });

        $(document).on('click', '.add-stock-row-variant', (e) => {
            const container = $(e.target).closest('.card');
            const variantIndex = $(e.target).data('variant-index');
            this.modules.variants.addStockRow(container, true, variantIndex);
        });

        $(document).on('click', '.remove-stock-row', (e) => {
            const row = $(e.target).closest('.stock-row, .variant-stock-row');
            const container = row.closest('.card');
            row.remove();
            this.modules.variants.updateTotalStock(container);
        });

        // Stock quantity change
        $(document).on('input', '.stock-quantity', (e) => {
            const container = $(e.target).closest('.card');
            this.modules.variants.updateTotalStock(container);
        });
    }

    /**
     * Setup wizard navigation (delegated to wizard module)
     */
    setupWizardNavigation() {
        // Wizard navigation is now handled by the wizard module
        console.log('🧙‍♂️ Wizard navigation delegated to wizard module');
    }

    /**
     * Setup cascading selects
     */
    setupCascadingSelects() {
        // Vendor change handler
        $('#vendor_id').on('change', (e) => {
            const vendorId = $(e.target).val();
            if (vendorId && $(e.target).attr('type') !== 'hidden') {
                this.loadDepartments(vendorId);
            }
        });

        // Department change handler
        $('#department_id').on('change', (e) => {
            const departmentId = $(e.target).val();
            if (departmentId) {
                this.loadCategories(departmentId);
            } else {
                this.clearSelect('#category_id');
                this.clearSelect('#sub_category_id');
            }
        });

        // Category change handler
        $('#category_id').on('change', (e) => {
            const categoryId = $(e.target).val();
            if (categoryId) {
                this.loadSubCategories(categoryId);
            } else {
                this.clearSelect('#sub_category_id');
            }
        });

        // Load departments on page load if vendor is set
        setTimeout(() => {
            const vendorId = $('#vendor_id').val();
            if (vendorId) {
                console.log('📦 Loading departments for vendor:', vendorId);
                this.loadDepartments(vendorId);
            }
        }, 200);
    }

    /**
     * Load departments for vendor
     */
    loadDepartments(vendorId) {
        const config = window.productFormConfig || {};
        const url = `${config.departmentsRoute || '/api/departments'}?vendor_id=${vendorId}`;

        this.fetchAndPopulateSelect(url, '#department_id', 'departments');
    }

    /**
     * Load categories for department
     */
    loadCategories(departmentId) {
        const config = window.productFormConfig || {};
        const url = `${config.categoriesRoute || '/api/categories'}?department_id=${departmentId}`;

        this.fetchAndPopulateSelect(url, '#category_id', 'categories');
    }

    /**
     * Load subcategories for category
     */
    loadSubCategories(categoryId) {
        const config = window.productFormConfig || {};
        const url = `${config.subCategoriesRoute || '/api/sub-categories'}?category_id=${categoryId}`;

        this.fetchAndPopulateSelect(url, '#sub_category_id', 'subcategories');
    }

    /**
     * Fetch data and populate select
     */
    fetchAndPopulateSelect(url, selectId, dataType) {
        const selectElement = $(selectId);
        const placeholder = `Select ${dataType.charAt(0).toUpperCase() + dataType.slice(1, -1)}`;

        // Show loading state
        selectElement.empty().append(`<option value="">Loading ${dataType}...</option>`);

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
            selectElement.empty().append(`<option value="">${placeholder}</option>`);

            const items = data.data || data;
            if (Array.isArray(items) && items.length > 0) {
                items.forEach(item => {
                    selectElement.append(`<option value="${item.id}">${item.name}</option>`);
                });
                console.log(`✅ Loaded ${items.length} ${dataType}`);
            } else {
                selectElement.append(`<option value="">No ${dataType} available</option>`);
                console.log(`⚠️ No ${dataType} found`);
            }
        })
        .catch(error => {
            console.error(`❌ Error loading ${dataType}:`, error);
            selectElement.empty().append(`<option value="">Error loading ${dataType}</option>`);
        });
    }

    /**
     * Clear select options
     */
    clearSelect(selectId) {
        const selectElement = $(selectId);
        const placeholder = selectElement.find('option:first').text();
        selectElement.empty().append(`<option value="">${placeholder}</option>`);
    }

    /**
     * Scroll to top of page
     */
    scrollToTop() {
        $('html, body').animate({ scrollTop: 0 }, 300);
    }

    /**
     * Get current configuration
     */
    getConfig() {
        return window.productFormConfig || {};
    }

    /**
     * Get module instance
     */
    getModule(name) {
        return this.modules[name];
    }

    /**
     * Destroy form instance
     */
    destroy() {
        // Destroy modules
        Object.values(this.modules).forEach(module => {
            if (module && typeof module.destroy === 'function') {
                module.destroy();
            }
        });

        // Remove event listeners
        $('#vendor_id, #department_id, #category_id').off('change');
        $(document).off('click', '.add-stock-row, .add-stock-row-variant, .remove-stock-row');
        $(document).off('input', '.stock-quantity');

        // Clear modules
        this.modules = {};
        this.isInitialized = false;

        console.log('🗑️ Product form destroyed');
    }
}

// Initialize when DOM is ready
$(document).ready(() => {
    // Create global instance
    window.productForm = new ProductForm();

    // Initialize the form
    window.productForm.init();
});

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductForm;
}
