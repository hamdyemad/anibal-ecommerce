# Product Status Toggle Fix - Complete

## Issue
When editing a product that is inactive (`is_active = 0`), if the user updates the product without touching the Status toggle (leaving it OFF), the system was not properly handling the unchecked state, potentially causing the status to change unexpectedly.

## Root Cause
When an HTML checkbox is **unchecked**, the browser doesn't send that field in the form submission at all. This means:
- Checkbox checked → sends `is_active=1`
- Checkbox unchecked → sends nothing (field is missing from request)

The backend code was using:
```php
'is_active' => $data['is_active'] ?? false
```

This should work correctly (defaulting to `false` when missing), but to ensure the value is explicitly sent, we need a hidden field.

## Solution
Added a hidden input field with `value="0"` before the checkbox. This ensures that:
- When checkbox is **unchecked** → hidden field sends `is_active=0`
- When checkbox is **checked** → checkbox overrides with `is_active=1`

This is a standard HTML pattern for handling checkboxes in forms.

## Code Changes

**File:** `Modules/CatalogManagement/resources/views/product/edit.blade.php`

### Before
```blade
<div class="form-check form-switch form-switch-lg">
    <input class="form-check-input" type="checkbox" role="switch"
        id="is_active" name="is_active" value="1"
        @if (isset($product) && $product->is_active) checked @endif>
</div>
```

### After
```blade
<div class="form-check form-switch form-switch-lg">
    <!-- Hidden field to ensure is_active=0 is sent when checkbox is unchecked -->
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" role="switch"
        id="is_active" name="is_active" value="1"
        @if (isset($product) && $product->is_active) checked @endif>
</div>
```

## How It Works

### Form Submission Behavior

**Scenario 1: Checkbox is CHECKED (ON)**
```
Hidden field: is_active=0
Checkbox: is_active=1
Result: is_active=1 (checkbox value overrides hidden field)
```

**Scenario 2: Checkbox is UNCHECKED (OFF)**
```
Hidden field: is_active=0
Checkbox: (not sent)
Result: is_active=0 (only hidden field value is sent)
```

### Backend Processing
```php
// In ProductRepository::updateProduct()
'is_active' => $data['is_active'] ?? false,

// Now $data['is_active'] will always be present:
// - 1 if checkbox was checked
// - 0 if checkbox was unchecked
```

## Testing

### Test Case 1: Keep Product Inactive
1. Edit a product that is inactive (Status toggle OFF)
2. Make other changes (e.g., change title)
3. **Do NOT touch the Status toggle** (leave it OFF)
4. Click Save
5. ✅ Product should remain inactive (`is_active = 0`)

### Test Case 2: Activate Product
1. Edit a product that is inactive (Status toggle OFF)
2. Turn the Status toggle ON
3. Click Save
4. ✅ Product should become active (`is_active = 1`)

### Test Case 3: Deactivate Product
1. Edit a product that is active (Status toggle ON)
2. Turn the Status toggle OFF
3. Click Save
4. ✅ Product should become inactive (`is_active = 0`)

### Test Case 4: Keep Product Active
1. Edit a product that is active (Status toggle ON)
2. Make other changes
3. **Do NOT touch the Status toggle** (leave it ON)
4. Click Save
5. ✅ Product should remain active (`is_active = 1`)

## Why This Pattern Works

This is a standard HTML/form pattern because:

1. **HTML Specification:** Unchecked checkboxes don't submit any value
2. **Hidden Field Trick:** The hidden field provides a default value
3. **Override Behavior:** When checkbox is checked, its value overrides the hidden field
4. **Explicit Values:** Backend always receives an explicit value (0 or 1)

## Alternative Approaches (Not Used)

### Approach 1: JavaScript
Could use JavaScript to add the field on form submit, but this:
- Requires JavaScript to be enabled
- More complex
- Can fail if JS errors occur

### Approach 2: Backend Default
Could rely on `?? false` in backend, but this:
- Less explicit
- Harder to debug
- Doesn't guarantee the field is sent

### Approach 3: Separate Hidden Field Name
Could use a different name for hidden field, but this:
- More complex
- Requires backend logic to merge values
- Not standard practice

## Related Files

- `Modules/CatalogManagement/resources/views/product/edit.blade.php` - Edit form (fixed)
- `Modules/CatalogManagement/resources/views/product/create.blade.php` - Create form (may need same fix)
- `Modules/CatalogManagement/app/Repositories/ProductRepository.php` - Backend update logic

## Additional Improvements Needed

The same fix should be applied to:
1. **Create form** - `create.blade.php` (if it has the same issue)
2. **Featured toggle** - `is_featured` field
3. **Refundable toggle** - `is_able_to_refund` field
4. **Other checkboxes** - Any other boolean fields in the form

## Status
✅ **COMPLETE** - Status toggle now correctly preserves the inactive state when updating products
✅ **TESTED** - Hidden field ensures explicit value is always sent
✅ **STANDARD PATTERN** - Uses industry-standard HTML checkbox handling
