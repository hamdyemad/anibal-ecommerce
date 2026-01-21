# Order Negative Total Validation

## Issue
When creating or updating orders, if the total becomes negative (due to excessive discounts, promo codes, or points), the system was silently setting it to 0 using `max(0, $totalPrice)`. This could lead to confusion and incorrect order processing.

## Solution
Added validation to prevent orders with negative totals from being created or updated. The system now throws an exception with a clear error message when the calculated total is less than 0.

## Changes Made

### 1. Updated CalculateFinalTotal Pipeline
**File:** `Modules/Order/app/Pipelines/CalculateFinalTotal.php`

**Before:**
```php
$context['total_price'] = max(0, $totalPrice);
```

**After:**
```php
// Validate that total is not negative
if ($totalPrice < 0) {
    throw new \Exception(trans('order::order.total_cannot_be_negative', [
        'total' => number_format($totalPrice, 2)
    ]));
}

$context['total_price'] = $totalPrice;
```

### 2. Added Translation Messages

**English** (`Modules/Order/lang/en/order.php`):
```php
'total_cannot_be_negative' => 'Order total cannot be negative. Current total: :total. Please adjust discounts, promo codes, or points.',
```

**Arabic** (`Modules/Order/lang/ar/order.php`):
```php
'total_cannot_be_negative' => 'لا يمكن أن يكون إجمالي الطلب سالبًا. الإجمالي الحالي: :total. يرجى تعديل الخصومات أو أكواد الترويج أو النقاط.',
```

## How It Works

### Order Total Calculation
The total is calculated as:
```
Total = Subtotal + Shipping + Fees + Tax - Discounts - Promo Discount - Points Cost
```

### Validation Trigger
The validation occurs in the `CalculateFinalTotal` pipeline step, which is executed during both:
- Order creation (`POST /orders`)
- Order update (`PUT /orders/{id}`)

### Error Response
When validation fails, the API returns:
```json
{
    "status": false,
    "message": "Order total cannot be negative. Current total: -50.00. Please adjust discounts, promo codes, or points.",
    "errors": ["Order total cannot be negative. Current total: -50.00. Please adjust discounts, promo codes, or points."]
}
```

HTTP Status: `422 Unprocessable Entity`

## Common Scenarios That Trigger This Error

1. **Excessive Promo Code Discount**
   - Subtotal: 100 EGP
   - Promo code: 150 EGP off
   - Result: Total = -50 EGP ❌

2. **Combined Discounts Exceed Subtotal**
   - Subtotal: 200 EGP
   - Manual discount: 100 EGP
   - Promo code: 50% (100 EGP)
   - Points: 50 EGP
   - Result: Total = 200 - 100 - 100 - 50 = -50 EGP ❌

3. **Points Usage Exceeds Order Value**
   - Subtotal: 50 EGP
   - Points used: 100 EGP worth
   - Result: Total = -50 EGP ❌

## User Action Required
When this error occurs, users need to:
1. Reduce the discount amount
2. Use a smaller promo code value
3. Reduce points usage
4. Add more products to increase the subtotal

## Files Modified
1. `Modules/Order/app/Pipelines/CalculateFinalTotal.php` - Added validation logic
2. `Modules/Order/lang/en/order.php` - Added English error message
3. `Modules/Order/lang/ar/order.php` - Added Arabic error message

## Testing
To test this validation:
1. Go to `/admin/orders/create`
2. Add products with a subtotal of 100 EGP
3. Apply a promo code with 150 EGP discount
4. Try to create the order
5. Should receive error: "Order total cannot be negative. Current total: -50.00..."

## Benefits
- ✅ Prevents invalid orders with negative totals
- ✅ Clear error message helps users understand the issue
- ✅ Shows the exact negative amount for debugging
- ✅ Guides users to adjust discounts/promo codes/points
- ✅ Maintains data integrity in the orders table
