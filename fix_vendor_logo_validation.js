// PATCH FOR: Modules/Vendor/resources/assets/js/vendor-form.js
// LOCATION: Around line 952-970
// ISSUE: Logo validation doesn't recognize existing logo from vendor request

// REPLACE THIS CODE:
/*
        // Check if this is edit mode (has existing vendor data)
        const isEditMode = $('input[name="_method"][value="PUT"]').length > 0 || 
                          $('input[name="translations"]').filter(function() { return $(this).val() !== ''; }).length > 0;

        // Validate Logo (only for new vendor)
        if (!isEditMode) {
            const logoInput = stepElement.find('input[name="logo"]');
            const logoPreviewContainer = stepElement.find('#logo-preview-container');
            const hasExistingLogo = logoPreviewContainer.find('img').length > 0 || logoInput.data('has-image') === true;
            const logoFile = logoInput[0]?.files?.length > 0;

            if (!hasExistingLogo && !logoFile) {
                const config = window.vendorFormConfig;
                errors.push({
                    field: 'logo',
                    message: config?.errorMessages?.logoRequired || 'Logo is required',
                    element: logoPreviewContainer.length ? logoPreviewContainer : logoInput
                });
            }
        }
*/

// WITH THIS CODE:
        // Check if this is edit mode (has existing vendor data) OR vendor request with logo
        const isEditMode = $('input[name="_method"][value="PUT"]').length > 0 || 
                          $('input[name="translations"]').filter(function() { return $(this).val() !== ''; }).length > 0;
        
        // Check if there's a vendor request (which already has a logo)
        const hasVendorRequest = $('input[name="vendor_request_id"]').length > 0 && 
                                $('input[name="vendor_request_id"]').val() !== '';

        // Validate Logo (only for new vendor WITHOUT vendor request)
        if (!isEditMode && !hasVendorRequest) {
            const logoInput = stepElement.find('input[name="logo"]');
            const logoPreviewContainer = stepElement.find('#logo-preview-container');
            const hasExistingLogo = logoPreviewContainer.find('img').length > 0 || logoInput.data('has-image') === true;
            const logoFile = logoInput[0]?.files?.length > 0;

            if (!hasExistingLogo && !logoFile) {
                const config = window.vendorFormConfig;
                errors.push({
                    field: 'logo',
                    message: config?.errorMessages?.logoRequired || 'Logo is required',
                    element: logoPreviewContainer.length ? logoPreviewContainer : logoInput
                });
            }
        }

// INSTRUCTIONS:
// 1. Open: Modules/Vendor/resources/assets/js/vendor-form.js
// 2. Find the section around line 952-970 (search for "Validate Logo")
// 3. Replace the code as shown above
// 4. Save the file
// 5. Run: npm run build (or the appropriate build command for your project)
// 6. Clear browser cache and test
