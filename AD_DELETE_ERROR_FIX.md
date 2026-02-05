# Ad Delete Error Fix

## Issue
When attempting to delete an ad (ID 11), the system threw an error:
```
Error deleting ad: No query results for model [Modules\SystemSetting\app\Models\Ad] 11
```

## Root Cause
The ad with ID 11 no longer exists in the database (likely deleted by the migration that cleaned up ad positions). When the delete method tried to find the ad using `findOrFail()`, it threw a `ModelNotFoundException`, but the controller wasn't catching this specific exception type.

## Solution
Updated the `destroy` method in `AdController` to specifically catch `ModelNotFoundException` and return a proper 404 response with a user-friendly message.

### Before
```php
public function destroy($lang, $code, $id)
{
    try {
        $this->adService->deleteAd($id);
        // ...
    } catch (\Exception $e) {
        // Generic error handling
    }
}
```

### After
```php
public function destroy($lang, $code, $id)
{
    try {
        $this->adService->deleteAd($id);
        // ...
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => __('systemsetting::ads.not_found')
        ], 404);
    } catch (\Exception $e) {
        // Generic error handling
    }
}
```

## Benefits
1. **Better error handling**: Distinguishes between "not found" and other errors
2. **Proper HTTP status**: Returns 404 for missing resources instead of 500
3. **User-friendly message**: Shows "Ad not found" instead of technical error
4. **Prevents confusion**: Users understand the ad doesn't exist rather than seeing a system error

## Files Modified
- `Modules/SystemSetting/app/Http/Controllers/AdController.php`
  - Added specific catch for `ModelNotFoundException`
  - Returns 404 status with appropriate message

## Testing
1. Try to delete an ad that doesn't exist
2. ✅ Should show "Ad not found" message instead of error
3. Try to delete an existing ad
4. ✅ Should delete successfully

## Translation Key Used
- `systemsetting::ads.not_found` - Already exists in the language files
