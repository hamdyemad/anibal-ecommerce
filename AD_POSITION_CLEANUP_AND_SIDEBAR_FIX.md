# Ad Position Cleanup and Sidebar Ad Reference Image Fix

## Tasks Completed

### 1. Migration to Clean Up Ad Positions
Created migration `2026_02_04_174705_clean_main_category_ad_positions.php` that:
- Deletes all ad positions containing "Main Category" in their name
- Deletes "Middle Home Ad" position with 300x300 dimensions
- Deletes all ads associated with these positions
- Creates a new clean "Main Category" position (300x800, web)

**Migration Status**: Already ran (Batch 91)

**Note**: The migration has already executed. To re-run with updated logic, you would need to:
1. Manually rollback: `php artisan migrate:rollback --step=1`
2. Re-run: `php artisan migrate`

### 2. Sidebar Ad Reference Image Fix
**Issue**: When selecting "Sidebar Ad" position in the ad form, no reference image was displayed.

**Root Cause**: The JavaScript mapping in `form.blade.php` was missing "Sidebar Ad" entry, even though:
- The file `sidebarad.png` exists in `public/ads/`
- The AdPosition model already had the mapping

**Solution**: Added "Sidebar Ad" to the JavaScript image mapping in the form blade file.

## Files Modified

### Migration
- `database/migrations/2026_02_04_174705_clean_main_category_ad_positions.php`
  - Cleans up "Main Category" positions
  - Removes "Middle Home Ad" with 300x300 dimensions
  - Creates new clean "Main Category" position

### Ad Position Reference Images
- `Modules/SystemSetting/resources/views/ads/form.blade.php`
  - Added "Sidebar Ad" => "sidebarad.png" to JavaScript mapping

## Reference Image Mappings (Complete List)

| Position Name | Image File |
|--------------|------------|
| Homepage Left Upper Ad Card | Homepage Left Upper Ad Card.png |
| Homepage Left Lower Ad Card | Homepage Left Lower Ad Card.png |
| Homepage Main Right Banner | Homepage Main Right Banner.png |
| Homepage Mid-Content Banner | Homepage Mid-Content Banner.png |
| Middle Home Ad | mobile-middle.jpg |
| Mobile Middle Banner | mobile-middle.jpg |
| Sidebar Ad | sidebarad.png |
| Main Category | (no reference image yet) |

## Testing

### Sidebar Ad Reference Image
1. Navigate to: `http://127.0.0.1:8000/en/eg/admin/system-settings/ads/4/edit`
2. Select "Sidebar Ad" from position dropdown
3. ✅ Verify that `sidebarad.png` is displayed as reference image

### Main Category Position
1. Check that old "Main Category" positions are removed
2. Verify new "Main Category" position exists with 300x800 dimensions
3. Note: No reference image configured for Main Category yet

## Build Status
✅ Assets rebuilt successfully with `npm run build`
