# Banner Red Border Validation Fix - Complete

## Issue
When validation errors occurred on the vendor creation form and the user clicked "Next", the logo box would get a red border to indicate the error, but the banner box did not get the same visual treatment. This created an inconsistent user experience.

**Visual Issue:**
- Logo box: Red border appears ✓
- Banner box: No red border ✗

## Root Cause
In the `displayStepErrors` function in `vendor-form.js`, the code that adds red borders to image preview containers (logo and banner) was commented out. The logic was there but disabled:

```javascript
// Lines 1165-1185 (before fix)
else if (element.hasClass('image-preview-container') || ...) {
    // element.addClass('border-danger is-invalid');  // ❌ COMMENTED OUT
    ...
}
else if (element.closest('.image-upload-wrapper').length || ...) {
    const previewContainer = wrapper.find('.image-preview-container');
    if (previewContainer.length) {
        // previewContainer.addClass('border-danger is-invalid');  // ❌ COMMENTED OUT
    }
    // element.siblings('.image-preview-container').addClass('border-danger is-invalid');  // ❌ COMMENTED OUT
}
```

## Solution
Uncommented the lines that add the `border-danger` and `is-invalid` classes to image preview containers. This ensures both logo and banner boxes get the red border treatment when validation fails.

```javascript
// Lines 1165-1185 (after fix)
else if (element.hasClass('image-preview-container') || ...) {
    element.addClass('border-danger is-invalid');  // ✅ ACTIVE
    ...
}
else if (element.closest('.image-upload-wrapper').length || ...) {
    const previewContainer = wrapper.find('.image-preview-container');
    if (previewContainer.length) {
        previewContainer.addClass('border-danger is-invalid');  // ✅ ACTIVE
    }
    element.siblings('.image-preview-container').addClass('border-danger is-invalid');  // ✅ ACTIVE
}
```

## Technical Details

**File Modified:**
- `Modules/Vendor/resources/assets/js/vendor-form.js` (lines ~1165-1185)

**Changes:**
1. Uncommented `element.addClass('border-danger is-invalid');` for preview containers
2. Uncommented `previewContainer.addClass('border-danger is-invalid');` for wrapper-based containers
3. Uncommented `element.siblings('.image-preview-container').addClass('border-danger is-invalid');` for sibling containers

**Build Command:**
```bash
npm run build
```

## How It Works

When validation fails for logo or banner:

1. **validateCurrentStep()** detects missing logo/banner
2. **displayStepErrors()** is called with the error
3. The error element is identified (either the input or preview container)
4. Red border classes (`border-danger is-invalid`) are added to:
   - The preview container itself
   - Any sibling preview containers
   - Preview containers within the wrapper
5. Error message is displayed below the field

## Visual Result

Now both logo and banner boxes will show:
- ✅ Red border around the upload box
- ✅ Error message below: "Logo is required" / "Banner is required"
- ✅ Consistent visual feedback for both fields

## Testing

1. Go to vendor creation form: `/admin/vendors/create`
2. Fill in vendor name and departments
3. **Do NOT upload logo or banner**
4. Click "Next" button
5. Verify both logo and banner boxes now have red borders
6. Upload logo - verify red border disappears from logo box
7. Upload banner - verify red border disappears from banner box

## Status
✅ **COMPLETE** - Banner now shows red border on validation error, matching logo behavior
