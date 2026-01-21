# Order Stage Final Stage Consistency Fix

## Issue
When an order has multiple vendors with different stages (e.g., one "new", one "delivered"), the admin cancel button logic was inconsistent with the backend final stage definition.

## Root Cause
**Inconsistency between frontend and backend:**
- **Backend** (`OrderStage.php` line 44): `FINAL_STAGES = ['deliver', 'cancel']` - did NOT include 'refund'
- **Frontend** (`order-actions.blade.php` line 35-37): Checked for `['deliver', 'cancel', 'refund']` - DID include 'refund'

This meant:
- Frontend correctly treated refund as final (no cancel button shown)
- Backend allowed transitions FROM refund stage (incorrect behavior)
- The `isFinalStage()` method returned `false` for refund stages

## Solution
Updated `OrderStage.php` to include 'refund' in the `FINAL_STAGES` constant:

```php
const FINAL_STAGES = ['deliver', 'cancel', 'refund'];
```

## Impact
Now both frontend and backend consistently treat these stages as final:
1. **deliver** - Order has been delivered
2. **cancel** - Order has been cancelled
3. **refund** - Order has been refunded

## Behavior After Fix

### Cancel Button Display
- Shows when ANY vendor is NOT in a final stage (deliver, cancel, refund)
- Hidden when ALL vendors are in final stages

### Admin Stage Changes
When admin changes stages for all vendors:
- Vendors in final stages (deliver, cancel, refund) are automatically skipped
- Only vendors in non-final stages (new, in_progress) are updated
- Success message shows: "X of Y vendors updated" with details about skipped vendors

### Stage Transitions
- Cannot transition FROM any final stage (deliver, cancel, refund)
- Refund can only happen AFTER deliver
- Cancel can happen from any non-final stage

## Files Modified
- `Modules/Order/app/Models/OrderStage.php` - Added 'refund' to FINAL_STAGES constant

## Testing Scenarios

### Scenario 1: Mixed Stages
- Order with 2 vendors: Vendor A = "new", Vendor B = "delivered"
- ✅ Cancel button shows (because Vendor A is not final)
- ✅ Clicking cancel only cancels Vendor A, skips Vendor B

### Scenario 2: All Final
- Order with 2 vendors: Vendor A = "delivered", Vendor B = "cancelled"
- ✅ Cancel button hidden (all vendors in final stages)
- ✅ Stage change button hidden

### Scenario 3: With Refund
- Order with 2 vendors: Vendor A = "refunded", Vendor B = "new"
- ✅ Cancel button shows (because Vendor B is not final)
- ✅ Clicking cancel only cancels Vendor B, skips Vendor A (refunded)

## Related Code
- `changeAllVendorStages()` in `OrderController.php` (line 842-968) - Already had correct logic to skip final stages
- `order-actions.blade.php` (line 35-37, 127) - Already had correct frontend logic
- `isFinalStage()` method (line 55-61) - Now returns true for refund stages
- `canTransitionTo()` method (line 77-127) - Now blocks transitions from refund stages
