# Fix: Vendor Logo Validation Issue

## Problem
When creating a vendor from a vendor request that already has a logo, the form still shows "Logo is required" error.

## Root Cause
The JavaScript validation is looking for `#logo-preview-container` but the actual ID is dynamic (e.g., `logo-12345-preview-container`), so it can't find the existing image.

## Solution
Update the JavaScript to:
1. Use class selector `.logo-preview-container` instead of ID
2. Check for vendor_request_id to skip validation when logo exists

## File to Modify
`Modules/Vendor/resources/assets/js/vendor-form.js`

## Find this code (around line 952-970):
```javascript
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
```

## Replace with:
```javascript
        // Check if this is edit mode (has existing vendor data)
        const isEditMode = $('input[name="_method"][value="PUT"]').length > 0 || 
                          $('input[name="translations"]').filter(function() { return $(this).val() !== ''; }).length > 0;
        
        // Check if there's a vendor request (which already has a logo)
        const hasVendorRequest = $('input[name="vendor_request_id"]').length > 0 && 
                                $('input[name="vendor_request_id"]').val() !== '';

        // Validate Logo (only for new vendor WITHOUT vendor request)
        if (!isEditMode && !hasVendorRequest) {
            const logoInput = stepElement.find('input[name="logo"]');
            // Use class selector instead of ID (ID is dynamic)
            const logoPreviewContainer = stepElement.find('.logo-preview-container');
            const hasExistingLogo = logoPreviewContainer.find('img').length > 0 || 
                                   logoInput.data('has-image') === true ||
                                   logoPreviewContainer.find('.preview-image').length > 0;
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
```

## Key Changes:
1. **Added vendor request check**: `hasVendorRequest` variable checks if there's a vendor_request_id
2. **Changed selector**: `#logo-preview-container` → `.logo-preview-container` (class instead of ID)
3. **Enhanced image detection**: Added check for `.preview-image` class
4. **Skip validation**: Only validate if NOT edit mode AND NOT vendor request

## After Making Changes:
1. Save the file
2. Run: `npm run build` (or your build command)
3. Clear browser cache (Ctrl+Shift+Delete)
4. Test the vendor creation from vendor request

## Expected Result:
✅ Logo validation should be skipped when creating vendor from a vendor request that already has a logo
✅ Logo validation should still work for new vendors without vendor request
