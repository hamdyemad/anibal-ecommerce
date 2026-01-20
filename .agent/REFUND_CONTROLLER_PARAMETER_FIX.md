# Refund Controller Parameter Name Fix

## Issue
The refund status change actions were failing with error: "No query results for model [Modules\Refund\app\Models\RefundRequest] 0"

## Root Cause
The routes defined the parameter as `{refundRequest}` but several controller methods were still using `$id` as the parameter name. Laravel expects the controller parameter name to match the route parameter name.

## Solution
Updated all controller methods to use `$refundRequest` as the parameter name to match the route definition:

### Updated Methods
1. `show($lang, $countryCode, $refundRequest)` - was `$id`
2. `markAsInProgress(Request $request, $refundRequest)` - was `$id`
3. `markAsPickedUp(Request $request, $refundRequest)` - was `$id`
4. `markAsRefunded(Request $request, $refundRequest)` - was `$id`
5. `updateNotes(UpdateRefundNotesRequest $request, $refundRequest)` - was `$id`

### Already Correct Methods
- `approve(Request $request, $refundRequest)` ✓
- `cancel(RejectRefundRequest $request, $refundRequest)` ✓

## Implementation Details
Each method now:
1. Accepts `$refundRequest` as the parameter (matching route)
2. Converts it to integer: `$id = (int) $refundRequest;`
3. Uses `$id` for all internal logic
4. Returns JSON responses for AJAX requests
5. Returns redirect responses for regular requests

## Files Modified
- `Modules/Refund/app/Http/Controllers/RefundRequestController.php`

## Testing
All status change actions should now work correctly:
- Approve refund
- Cancel refund (with reason)
- Mark as in progress
- Mark as picked up
- Mark as refunded
- Update notes

## Status
✅ Complete - All parameter names now match route definitions
