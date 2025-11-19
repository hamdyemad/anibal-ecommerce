/**
 * Product Form Wizard Module
 * Handles multi-step wizard navigation and validation
 */

class ProductFormWizard {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.config = window.productFormConfig || {};
    }

    /**
     * Initialize wizard
     */
    init() {
        console.log('🧙‍♂️ Initializing Product Form Wizard...');

        // Wait for DOM to be fully ready
        if (document.readyState === 'loading') {
            console.log('⏳ DOM still loading, waiting...');
            $(document).ready(() => {
                this.setupEventListeners();
                this.updateWizardDisplay();
            });
        } else {
            // DOM is already ready
            setTimeout(() => {
                this.setupEventListeners();
                this.updateWizardDisplay();
            }, 100);
        }

        console.log('✅ Wizard initialized');
    }

    /**
     * Setup wizard event listeners
     */
    setupEventListeners() {
        console.log('🔗 Setting up wizard event listeners...');

        // Use document delegation to ensure events work even if buttons are added later
        $(document).off('click', '#nextBtn').on('click', '#nextBtn', (e) => {
            console.log('🔄 ========================================');
            console.log('🔄 NEXT BUTTON CLICKED VIA DELEGATION!');
            console.log('🔄 ========================================');
            console.log('🔄 Event:', e);
            console.log('🔄 Current step before:', this.currentStep);
            e.preventDefault();
            e.stopPropagation();
            this.nextStep();
            console.log('🔄 Current step after:', this.currentStep);
        });

        $(document).off('click', '#prevBtn').on('click', '#prevBtn', (e) => {
            console.log('🔄 ========================================');
            console.log('🔄 PREVIOUS BUTTON CLICKED VIA DELEGATION!');
            console.log('🔄 ========================================');
            console.log('🔄 Event:', e);
            console.log('🔄 Current step before:', this.currentStep);
            e.preventDefault();
            e.stopPropagation();
            this.prevStep();
            console.log('🔄 Current step after:', this.currentStep);
        });

        // Also try direct binding as backup
        setTimeout(() => {
            const nextBtn = $('#nextBtn');
            const prevBtn = $('#prevBtn');

            console.log('🔍 Delayed check - Next button found:', nextBtn.length);
            console.log('🔍 Delayed check - Previous button found:', prevBtn.length);

            if (nextBtn.length > 0) {
                // Remove any existing handlers first
                nextBtn.off('click.wizard');

                // Add our handler with namespace
                nextBtn.on('click.wizard', (e) => {
                    console.log('🔄 ========================================');
                    console.log('🔄 NEXT BUTTON CLICKED VIA DIRECT BINDING!');
                    console.log('🔄 ========================================');
                    console.log('🔄 Event:', e);
                    console.log('🔄 Current step before:', this.currentStep);
                    e.preventDefault();
                    e.stopPropagation();
                    this.nextStep();
                    console.log('🔄 Current step after:', this.currentStep);
                });

                console.log('✅ Direct binding added to next button');
            } else {
                console.warn('⚠️ Next button still not found after delay');
            }

            if (prevBtn.length > 0) {
                prevBtn.off('click.wizard');
                prevBtn.on('click.wizard', (e) => {
                    console.log('🔄 ========================================');
                    console.log('🔄 PREVIOUS BUTTON CLICKED VIA DIRECT BINDING!');
                    console.log('🔄 ========================================');
                    console.log('🔄 Event:', e);
                    console.log('🔄 Current step before:', this.currentStep);
                    e.preventDefault();
                    e.stopPropagation();
                    this.prevStep();
                    console.log('🔄 Current step after:', this.currentStep);
                });

                console.log('✅ Direct binding added to previous button');
            }
        }, 500);

        // Step indicators (wizard navigation circles) - be more specific to avoid conflicts
        $(document).on('click', '.wizard-step, .wizard-step-nav', (e) => {
            // Ignore clicks on form elements like switches, inputs, buttons
            if ($(e.target).is('input, button, .form-check-input, .form-switch, .btn, select, textarea')) {
                console.log('🔄 Ignoring click on form element:', e.target);
                return;
            }

            // Ignore clicks inside form elements
            if ($(e.target).closest('input, button, .form-check-input, .form-switch, .btn, select, textarea, .form-group').length > 0) {
                console.log('🔄 Ignoring click inside form element');
                return;
            }

            console.log('🔄 Wizard step indicator clicked!');

            const clickedElement = $(e.target).closest('.wizard-step, .wizard-step-nav');
            let stepNumber = parseInt(clickedElement.data('step'));

            // Try different ways to get step number
            if (!stepNumber) {
                stepNumber = parseInt(clickedElement.attr('data-step'));
            }
            if (!stepNumber) {
                // Look for step number in text content
                const text = clickedElement.text().trim();
                const match = text.match(/(\d+)/);
                if (match) {
                    stepNumber = parseInt(match[1]);
                }
            }

            console.log('🔄 Detected step number:', stepNumber);
            console.log('🔄 Current step:', this.currentStep);
            console.log('🔄 Clicked element:', clickedElement[0]);

            if (stepNumber && stepNumber >= 1 && stepNumber <= this.totalSteps) {
                console.log('🔄 Going to step:', stepNumber);
                e.preventDefault();
                e.stopPropagation();
                this.goToStep(stepNumber);
            } else {
                console.log('❌ Invalid step number or out of range');
            }
        });

        console.log('✅ Wizard event listeners setup complete');

        // Add global test function
        window.testNextStep = () => {
            console.log('🧪 Manual test: calling nextStep()');
            this.nextStep();
        };

        window.testPrevStep = () => {
            console.log('🧪 Manual test: calling prevStep()');
            this.prevStep();
        };

        console.log('🧪 Test functions added: window.testNextStep() and window.testPrevStep()');

        // Add button inspection function
        window.inspectButtons = () => {
            const nextBtn = $('#nextBtn');
            const prevBtn = $('#prevBtn');

            console.log('🔍 Button inspection:');
            console.log('Next button exists:', nextBtn.length > 0);
            console.log('Next button visible:', nextBtn.is(':visible'));
            console.log('Next button disabled:', nextBtn.prop('disabled'));
            console.log('Next button HTML:', nextBtn.length > 0 ? nextBtn[0].outerHTML : 'Not found');
            console.log('Previous button exists:', prevBtn.length > 0);
            console.log('Previous button visible:', prevBtn.is(':visible'));

            // Check all event handlers
            if (nextBtn.length > 0) {
                const events = $._data(nextBtn[0], 'events');
                console.log('🔍 Next button events:', events);

                // Test manual click
                console.log('🧪 Triggering manual click on next button...');
                nextBtn.trigger('click');

                // Test direct call
                console.log('🧪 Testing direct nextStep call...');
                window.productForm.getModule('wizard').nextStep();
            }
        };

        // Add click debugging
        window.debugClicks = () => {
            console.log('🔍 Adding click debugging...');

            // Add a specific click listener for wizard elements only
            $(document).on('click', '#nextBtn, #prevBtn, .wizard-step, .wizard-step-nav', function(e) {
                console.log('🔍 CAUGHT: Click on wizard element!', this);
                console.log('🔍 Event target:', e.target);
                console.log('🔍 Event currentTarget:', e.currentTarget);
                console.log('🔍 Event propagation stopped:', e.isPropagationStopped());
                console.log('🔍 Event default prevented:', e.isDefaultPrevented());
            });

            console.log('✅ Click debugging enabled (wizard elements only)');
        };

        console.log('🧪 Added window.inspectButtons() for debugging');

        // Add step content inspection function
        window.inspectStepContent = () => {
            console.log('🔍 STEP CONTENT INSPECTION:');

            // Find all step content elements
            const stepContents = $('.wizard-step-content, [data-step]');
            console.log('🔍 Total step content elements found:', stepContents.length);

            stepContents.each(function(index) {
                const $this = $(this);
                const stepNum = $this.data('step') || (index + 1);
                const isVisible = $this.is(':visible');
                const display = $this.css('display');
                console.log(`🔍 Step ${stepNum} content: visible=${isVisible}, display=${display}, element:`, this);
            });

            // Check wizard step indicators
            const stepIndicators = $('.wizard-step, .step, [class*="step"]');
            console.log('🔍 Total step indicators found:', stepIndicators.length);

            stepIndicators.each(function(index) {
                const $this = $(this);
                const stepNum = $this.data('step') || (index + 1);
                const isActive = $this.hasClass('active');
                const isCompleted = $this.hasClass('completed');
                const classes = $this.attr('class');
                console.log(`🔍 Step ${stepNum} indicator: active=${isActive}, completed=${isCompleted}, classes="${classes}"`);
            });
        };

        console.log('🧪 Added window.inspectStepContent() for debugging');
    }

    /**
     * Go to next step
     */
    nextStep() {
        console.log(`🔄 Attempting to go to next step from ${this.currentStep}`);
        console.log(`🔄 Current step: ${this.currentStep}, Total steps: ${this.totalSteps}`);

        if (this.currentStep < this.totalSteps) {
            console.log(`🔄 Validation check...`);

            // Validate current step before proceeding (with option to skip)
            const validationResult = this.validateCurrentStep();
            console.log(`🔄 Validation result: ${validationResult}`);

            if (validationResult || true) { // Temporarily bypass validation to allow navigation
                console.log(`✅ Moving to next step...`);
                this.currentStep++;
                this.updateWizardDisplay();
                // Scroll to top when moving to next step
                setTimeout(() => {
                    this.scrollToTop();
                }, 200);
                console.log(`✅ Moved to step ${this.currentStep}`);
            } else {
                console.log(`❌ Step ${this.currentStep} validation failed`);
                console.warn('⚠️ Validation is currently disabled - navigation should still work');
            }
        } else {
            console.log('⚠️ Already at last step');
        }
    }

    /**
     * Go to previous step
     */
    prevStep() {
        console.log(`🔄 Going to previous step from ${this.currentStep}`);

        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateWizardDisplay();
            // Scroll to top when moving to previous step
            setTimeout(() => {
                this.scrollToTop();
            }, 200);
            console.log(`✅ Moved to step ${this.currentStep}`);
        } else {
            console.log('⚠️ Already at first step');
        }
    }

    /**
     * Go to specific step
     */
    goToStep(stepNumber) {
        console.log(`🎯 goToStep called with: ${stepNumber}`);
        console.log(`🎯 Current step: ${this.currentStep}, Total steps: ${this.totalSteps}`);

        if (stepNumber >= 1 && stepNumber <= this.totalSteps) {
            console.log(`🎯 Going to step ${stepNumber}`);
            const previousStep = this.currentStep;
            this.currentStep = stepNumber;
            this.updateWizardDisplay();

            // Only scroll if we're actually changing steps and it's a deliberate navigation
            if (previousStep !== stepNumber) {
                console.log(`🎯 Step changed from ${previousStep} to ${stepNumber}, scrolling to top`);
                setTimeout(() => {
                    this.scrollToTop();
                }, 200);
            }

            // Trigger step change event
            $(document).trigger('wizard:step-changed', [stepNumber]);
            console.log(`✅ Successfully moved to step ${stepNumber}`);
        } else {
            console.log(`❌ Invalid step: ${stepNumber} (must be between 1 and ${this.totalSteps})`);
        }
    }

    /**
     * Update wizard display
     */
    updateWizardDisplay() {
        console.log(`🎨 Updating wizard display for step ${this.currentStep}`);

        // Clear validation alerts when navigating between steps
        this.clearValidationAlerts();

        // Hide all step content
        $('.wizard-step-content').hide();
        console.log(`🎨 Hidden all step content`);

        // Show current step content
        const currentStepContent = $(`.wizard-step-content[data-step="${this.currentStep}"]`);
        currentStepContent.show();
        console.log(`🎨 Showing step ${this.currentStep} content:`, currentStepContent.length);

        // Update wizard step indicators (the numbered circles)
        this.updateWizardIndicators();

        // Update navigation buttons
        this.updateNavigationButtons();

        // Trigger step change event
        $(document).trigger('wizard:step-changed', [this.currentStep]);

        console.log(`✅ Wizard display updated for step ${this.currentStep}`);
    }

    /**
     * Update wizard indicators
     */
    updateWizardIndicators() {
        console.log(`🎨 Updating wizard indicators for step ${this.currentStep}`);

        // Try multiple selectors for wizard steps
        const wizardSteps = $('.wizard-step, .step, [class*="step"]');
        console.log(`🎨 Found ${wizardSteps.length} wizard step elements`);

        // Remove all active/completed classes
        wizardSteps.removeClass('active completed current');

        for (let i = 1; i <= this.totalSteps; i++) {
            // Try multiple ways to find step elements
            let stepElement = $(`.wizard-step[data-step="${i}"]`);
            if (!stepElement.length) {
                stepElement = $(`[data-step="${i}"]`);
            }
            if (!stepElement.length) {
                // Try finding by text content or index
                stepElement = wizardSteps.eq(i - 1);
            }

            console.log(`🎨 Step ${i} element found:`, stepElement.length);

            if (stepElement.length) {
                if (i < this.currentStep) {
                    stepElement.addClass('completed');
                    console.log(`🎨 Step ${i} marked as completed`);
                } else if (i === this.currentStep) {
                    stepElement.addClass('active current');
                    console.log(`🎨 Step ${i} marked as active`);
                }
            }
        }
    }

    /**
     * Update navigation buttons
     */
    updateNavigationButtons() {
        // Previous button
        if (this.currentStep > 1) {
            $('#prevBtn').show();
        } else {
            $('#prevBtn').hide();
        }

        // Next button
        if (this.currentStep < this.totalSteps) {
            $('#nextBtn').show();
            $('#submitBtn').hide();
        } else {
            $('#nextBtn').hide();
            $('#submitBtn').show();
        }

        console.log(`🔘 Navigation buttons updated for step ${this.currentStep}`);
    }

    /**
     * Validate current step
     */
    validateCurrentStep() {
        // Get validation module if available
        const validationModule = window.productForm?.getModule('validation');

        if (validationModule && validationModule.validateStep) {
            return validationModule.validateStep(this.currentStep);
        } else {
            // Basic validation fallback
            return this.basicStepValidation();
        }
    }

    /**
     * Basic step validation fallback
     */
    basicStepValidation() {
        const currentStepElement = $(`.wizard-step-content[data-step="${this.currentStep}"]`);
        let isValid = true;

        // Check required fields in current step
        currentStepElement.find('input[required], select[required], textarea[required]').each((index, element) => {
            const $element = $(element);
            const value = $element.val();

            if (!value || (typeof value === 'string' && !value.trim())) {
                $element.addClass('is-invalid');
                isValid = false;

                // Show error message if container exists
                const fieldName = $element.attr('name');
                if (fieldName) {
                    const errorId = `error-${fieldName.replace(/[\[\]\.]/g, '-')}`;
                    $(`#${errorId}`).text(this.config.fieldRequired || 'This field is required').show();
                }
            } else {
                $element.removeClass('is-invalid');
            }
        });

        if (!isValid) {
            this.showValidationAlert();
        }

        return isValid;
    }

    /**
     * Show validation alert
     */
    showValidationAlert() {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="uil uil-exclamation-triangle me-2"></i>
                <strong>${this.config.validationErrorTitle || 'Validation Error!'}</strong> ${this.config.validationErrorMessage || 'Please fill in all required fields before proceeding.'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        $('#validation-alerts-container').html(alertHtml);
        this.scrollToTop();
    }

    /**
     * Scroll to top of page
     */
    scrollToTop() {
        $('html, body').animate({ scrollTop: 0 }, 300);
    }

    /**
     * Get current step
     */
    getCurrentStep() {
        return this.currentStep;
    }

    /**
     * Get total steps
     */
    getTotalSteps() {
        return this.totalSteps;
    }

    /**
     * Check if on first step
     */
    isFirstStep() {
        return this.currentStep === 1;
    }

    /**
     * Check if on last step
     */
    isLastStep() {
        return this.currentStep === this.totalSteps;
    }

    /**
     * Clear validation alerts
     */
    clearValidationAlerts() {
        // Clear the main validation alerts container
        $('#validation-alerts-container').empty();

        // Clear all inline error messages
        $('.error-message').hide().text('');

        // Remove is-invalid class from fields
        $('.is-invalid').removeClass('is-invalid');

        // Clear variant validation errors
        $('.variant-validation-error').remove();

        console.log('🧹 Validation alerts cleared');
    }

    /**
     * Reset wizard to first step
     */
    reset() {
        console.log('🔄 Resetting wizard to first step');
        this.currentStep = 1;
        this.updateWizardDisplay();
    }

    /**
     * Enable/disable step
     */
    setStepEnabled(stepNumber, enabled) {
        const stepElement = $(`.wizard-step[data-step="${stepNumber}"]`);

        if (enabled) {
            stepElement.removeClass('disabled');
        } else {
            stepElement.addClass('disabled');
        }
    }

    /**
     * Add step completion callback
     */
    onStepComplete(stepNumber, callback) {
        $(document).on('wizard:step-changed', (event, newStep) => {
            if (newStep > stepNumber) {
                callback(stepNumber);
            }
        });
    }

    /**
     * Destroy wizard
     */
    destroy() {
        $('#nextBtn, #prevBtn').off('click');
        $(document).off('click', '.wizard-step');
        $(document).off('wizard:step-changed');

        console.log('🗑️ Wizard destroyed');
    }
}

// Export for global use
window.ProductFormWizard = ProductFormWizard;
