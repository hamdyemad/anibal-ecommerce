# Ad Position "Middle Home Ad" Reference Image Fix

## Issue
The "Middle Home Ad" position was showing the wrong reference image (Homepage Mid-Content Banner instead of mobile-middle.jpg) in the ad form.

## Root Cause
The JavaScript mapping in the form blade file was using fuzzy matching with `stripos() !== false`, which caused "Middle Home Ad" to incorrectly match "Homepage Mid-Content Banner" because "Mid" is found in "Middle".

## Solution
Updated the position reference image mapping logic in the form blade file to:
1. **First try exact match** (case-insensitive) using `strcasecmp()`
2. **Then try "starts with" match** using `stripos() === 0` only if no exact match found

This ensures:
- "Middle Home Ad" matches exactly to `mobile-middle.jpg`
- "Homepage Mid-Content Banner" matches exactly to `Homepage Mid-Content Banner.png`
- No false positives from partial string matches

## Files Modified
- `Modules/SystemSetting/resources/views/ads/form.blade.php`
  - Updated the PHP code in JavaScript section that builds `positionReferenceImages` object
  - Changed from fuzzy matching to exact match first, then "starts with" match

## Testing
1. Navigate to ad create/edit form
2. Select "Middle Home Ad" position from dropdown
3. Verify that `mobile-middle.jpg` is displayed as the reference image
4. Test other positions to ensure they still show correct images:
   - Homepage Left Upper Ad Card → Homepage Left Upper Ad Card.png
   - Homepage Left Lower Ad Card → Homepage Left Lower Ad Card.png
   - Homepage Main Right Banner → Homepage Main Right Banner.png
   - Homepage Mid-Content Banner → Homepage Mid-Content Banner.png

## Build Status
✅ Assets rebuilt successfully with `npm run build`
