# Frontend Integration Guide for Variant Link ID

## Overview
This guide explains how to integrate the `variant_link_id` feature into the product form frontend to properly track parent-child variant relationships.

## Problem Being Solved
When a variant (e.g., "Small") is linked to multiple parent variants (e.g., both "Red" and "Blue"), we need to know which specific parent-child relationship was used when creating a product variant.

## Frontend Changes Required

### 1. Product Form - Variant Selection

When the user selects a variant path (e.g., Color: Red → Size: Small), you need to:

1. Track both the parent and child variant IDs during selection
2. Fetch the link ID before form submission
3. Include the link ID in the variant data

### 2. Implementation Steps

#### Step 1: Track Parent-Child Selection
When building the variant selection UI, keep track of the parent variant ID:

```javascript
// Example variant selection state
const variantSelection = {
    parentId: 3,        // Red (Color)
    childId: 5,         // Small (Size)
    variantPath: ['Red', 'Small']
};
```

#### Step 2: Fetch Link ID
Before submitting the form, fetch the link ID using the API:

```javascript
async function getVariantLinkId(parentId, childId) {
    try {
        const response = await fetch(
            `/admin/variants-configurations/get-link-id?parent_id=${parentId}&child_id=${childId}`
        );
        const data = await response.json();
        
        if (data.success) {
            return data.link_id;
        } else {
            console.warn('Link not found between variants:', parentId, childId);
            return null;
        }
    } catch (error) {
        console.error('Error fetching variant link ID:', error);
        return null;
    }
}
```

#### Step 3: Include in Form Submission
When preparing the variant data for submission:

```javascript
async function prepareVariantData(variant) {
    const variantData = {
        variant_configuration_id: variant.childId,  // The final selected variant
        price: variant.price,
        sku: variant.sku,
        has_discount: variant.hasDiscount,
        price_before_discount: variant.priceBeforeDiscount,
        discount_end_date: variant.discountEndDate,
        stocks: variant.stocks
    };
    
    // If there's a parent-child relationship, get the link ID
    if (variant.parentId && variant.childId) {
        const linkId = await getVariantLinkId(variant.parentId, variant.childId);
        if (linkId) {
            variantData.variant_link_id = linkId;
        }
    }
    
    return variantData;
}
```

#### Step 4: Submit Product Form
```javascript
async function submitProductForm() {
    const formData = {
        // ... other product fields
        configuration_type: 'variants',
        variants: []
    };
    
    // Prepare each variant with link ID
    for (const variant of selectedVariants) {
        const variantData = await prepareVariantData(variant);
        formData.variants.push(variantData);
    }
    
    // Submit to backend
    const response = await fetch('/admin/products', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData)
    });
    
    // Handle response...
}
```

### 3. Handling Different Scenarios

#### Scenario A: Direct Parent-Child (No Linking)
If "Small" is a direct child of "Red" (not using the linking feature):
- `variant_link_id` will be `null`
- System falls back to using only `variant_configuration_id`

#### Scenario B: Linked Variants
If "Small" is linked to "Red" through the linking feature:
- `variant_link_id` will contain the link ID
- System can track the exact parent-child relationship

#### Scenario C: Simple Products
For simple products (no variants):
- Don't include `variant_link_id`
- System handles it automatically

### 4. UI Considerations

#### Display Variant Path
Show the full path to help users understand the selection:

```javascript
function displayVariantPath(variant) {
    // Example: "Color: Red → Size: Small"
    return `${variant.parentKey}: ${variant.parentName} → ${variant.childKey}: ${variant.childName}`;
}
```

#### Validation
Ensure the link exists before submission:

```javascript
async function validateVariantSelection(parentId, childId) {
    const linkId = await getVariantLinkId(parentId, childId);
    
    if (!linkId) {
        alert('The selected variant combination is not valid. Please check the variant configuration.');
        return false;
    }
    
    return true;
}
```

### 5. Example: Complete Variant Selection Flow

```javascript
class VariantSelector {
    constructor() {
        this.selectedVariants = [];
    }
    
    async addVariant(parentId, childId, price, stocks) {
        // Validate the selection
        const linkId = await this.getVariantLinkId(parentId, childId);
        
        if (!linkId && parentId) {
            throw new Error('Invalid variant combination');
        }
        
        // Create variant object
        const variant = {
            variant_configuration_id: childId,
            variant_link_id: linkId,
            price: price,
            sku: this.generateSKU(),
            stocks: stocks
        };
        
        this.selectedVariants.push(variant);
        return variant;
    }
    
    async getVariantLinkId(parentId, childId) {
        if (!parentId || !childId) return null;
        
        const response = await fetch(
            `/admin/variants-configurations/get-link-id?parent_id=${parentId}&child_id=${childId}`
        );
        const data = await response.json();
        return data.success ? data.link_id : null;
    }
    
    generateSKU() {
        return `SKU-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    }
    
    getFormData() {
        return {
            configuration_type: 'variants',
            variants: this.selectedVariants
        };
    }
}

// Usage
const selector = new VariantSelector();

// User selects: Red → Small
await selector.addVariant(
    3,      // parentId: Red
    5,      // childId: Small
    200.00, // price
    [{ region_id: 1, quantity: 100 }]
);

// Get form data for submission
const formData = selector.getFormData();
```

## Testing

### Test Case 1: Linked Variant
1. Create a link: Red (ID: 3) → Small (ID: 5)
2. Select this combination in the product form
3. Verify `variant_link_id` is included in the submission
4. Check database: `vendor_product_variants.variant_link_id` should have the link ID

### Test Case 2: Multiple Links to Same Child
1. Create links: Red → Small, Blue → Small
2. Create two products: one with Red→Small, one with Blue→Small
3. Verify each has a different `variant_link_id`
4. Confirm you can distinguish which parent was used

### Test Case 3: Backward Compatibility
1. Create a product without `variant_link_id`
2. Verify it still works (field is nullable)
3. Update the product and add `variant_link_id`
4. Verify the update works correctly

## API Reference

### Get Link ID Endpoint
```
GET /admin/variants-configurations/get-link-id
```

**Parameters:**
- `parent_id` (required): Parent variant configuration ID
- `child_id` (required): Child variant configuration ID

**Response (Success):**
```json
{
    "success": true,
    "link_id": 10,
    "parent_id": 3,
    "child_id": 5
}
```

**Response (Not Found):**
```json
{
    "success": false,
    "message": "Link not found",
    "link_id": null
}
```

## Notes

1. The `variant_link_id` field is optional and nullable for backward compatibility
2. Only include it when there's an actual parent-child link relationship
3. For simple products or direct parent-child relationships, it can be omitted
4. The backend will handle null values gracefully
