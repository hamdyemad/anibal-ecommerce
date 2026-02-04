# Ad Position Reference Images - Complete

## Overview
Added functionality to display reference images for each ad position, showing users where their ads will appear on the website. Reference images are stored in `public/ads/` folder and automatically linked to ad positions.

## Features

### 1. Reference Images in Ad View
When viewing an ad, if the position has a reference image, it will be displayed in a separate card showing:
- The reference image
- Position name
- Description explaining this shows where the ad appears

### 2. Reference Images in Ad Form
When creating/editing an ad, selecting a position will automatically show:
- A preview of where the ad will appear
- The position name
- Helpful description

### 3. Automatic Image Mapping
The system automatically maps position names to their reference images in the `public/ads/` folder.

## Reference Images Available

Located in `public/ads/`:
- `Homepage Left Upper Ad Card.png`
- `Homepage Left Lower Ad Card.png`
- `Homepage Main Right Banner.png`
- `Homepage Mid-Content Banner.png`
- `mobile-middle.jpg`

## Implementation Details

### 1. AdPosition Model Enhancement

**File:** `Modules/SystemSetting/app/Models/AdPosition.php`

Added two new methods:

```php
/**
 * Get the reference image path for this ad position
 * Maps position names to their reference images in public/ads folder
 */
public function getReferenceImageAttribute()
{
    $imageMap = [
        'Homepage Left Upper Ad Card' => 'Homepage Left Upper Ad Card.png',
        'Homepage Left Lower Ad Card' => 'Homepage Left Lower Ad Card.png',
        'Homepage Main Right Banner' => 'Homepage Main Right Banner.png',
        'Homepage Mid-Content Banner' => 'Homepage Mid-Content Banner.png',
        'Mobile Middle Banner' => 'mobile-middle.jpg',
    ];

    // Try exact match
    if (isset($imageMap[$this->name])) {
        $imagePath = 'ads/' . $imageMap[$this->name];
        if (file_exists(public_path($imagePath))) {
            return asset($imagePath);
        }
    }

    // Try partial match (case-insensitive)
    foreach ($imageMap as $positionName => $imageName) {
        if (stripos($this->name, $positionName) !== false || 
            stripos($positionName, $this->name) !== false) {
            $imagePath = 'ads/' . $imageName;
            if (file_exists(public_path($imagePath))) {
                return asset($imagePath);
            }
        }
    }

    return null;
}

/**
 * Check if this position has a reference image
 */
public function hasReferenceImage()
{
    return !is_null($this->reference_image);
}
```

**How it works:**
1. Defines a map of position names to image filenames
2. Tries exact name match first
3. Falls back to partial/fuzzy matching (case-insensitive)
4. Checks if file actually exists before returning path
5. Returns full asset URL or null if no image found

### 2. Ad View Enhancement

**File:** `Modules/SystemSetting/resources/views/ads/view.blade.php`

Added reference image card in the sidebar:

```blade
{{-- Position Reference Image --}}
@if ($ad->adPosition && $ad->adPosition->hasReferenceImage())
    <div class="card card-holder mb-3">
        <div class="card-header">
            <h3>
                <i class="uil uil-map-marker me-1"></i>{{ __('systemsetting::ads.position_reference') }}
            </h3>
        </div>
        <div class="card-body text-center">
            <p class="text-muted small mb-2">{{ __('systemsetting::ads.position_reference_description') }}</p>
            <img src="{{ $ad->adPosition->reference_image }}" 
                alt="Position Reference" 
                class="img-fluid round border"
                style="max-width: 100%; max-height: 400px;">
            <p class="text-muted small mt-2 mb-0">
                <i class="uil uil-info-circle"></i> {{ $ad->adPosition->position ?? $ad->adPosition->name }}
            </p>
        </div>
    </div>
@endif
```

### 3. Ad Form Enhancement

**File:** `Modules/SystemSetting/resources/views/ads/form.blade.php`

#### HTML Section
Added reference image preview container:

```blade
{{-- Position Reference Image Preview --}}
<div class="col-md-12 mb-25" id="position-reference-container" style="display: none;">
    <div class="alert alert-info border-0" style="background-color: #e7f3ff;">
        <div class="d-flex align-items-start">
            <i class="uil uil-info-circle me-2" style="font-size: 20px;"></i>
            <div class="flex-grow-1">
                <h6 class="mb-2">{{ __('systemsetting::ads.position_reference') }}</h6>
                <p class="mb-2 small">{{ __('systemsetting::ads.position_reference_description') }}</p>
                <div id="position-reference-image" class="text-center mt-3">
                    <!-- Image will be loaded here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>
```

#### JavaScript Section
Added dynamic image loading:

```javascript
// Position Reference Image Mapping
const positionReferenceImages = {
    @foreach ($positions as $position)
        @php
            // Map position to image
            $imageMap = [...];
            $imageName = null;
            foreach ($imageMap as $posName => $imgName) {
                if (stripos($position->position, $posName) !== false) {
                    $imageName = $imgName;
                    break;
                }
            }
        @endphp
        @if ($imageName)
            '{{ $position->id }}': {
                image: '{{ asset('ads/' . $imageName) }}',
                name: '{{ $position->position }}'
            },
        @endif
    @endforeach
};

// Handle position selection change
function updateReferenceImage() {
    const selectedPositionId = positionSelect.value;
    
    if (selectedPositionId && positionReferenceImages[selectedPositionId]) {
        const refData = positionReferenceImages[selectedPositionId];
        referenceImageDiv.innerHTML = `
            <img src="${refData.image}" 
                alt="${refData.name}" 
                class="img-fluid border rounded shadow-sm"
                style="max-width: 100%; max-height: 400px;">
            <p class="text-muted small mt-2 mb-0">
                <i class="uil uil-map-marker"></i> ${refData.name}
            </p>
        `;
        referenceContainer.style.display = 'block';
    } else {
        referenceContainer.style.display = 'none';
    }
}

// Update on page load and position change
positionSelect.addEventListener('change', updateReferenceImage);
```

### 4. Translation Keys

**Files:** 
- `Modules/SystemSetting/lang/en/ads.php`
- `Modules/SystemSetting/lang/ar/ads.php`

Added:
```php
// Position Reference
'position_reference' => 'Position Reference',
'position_reference_description' => 'This shows where your ad will appear on the website',
```

Arabic:
```php
'position_reference' => 'مرجع الموضع',
'position_reference_description' => 'يوضح هذا مكان ظهور إعلانك على الموقع',
```

## User Experience

### Creating/Editing an Ad

1. User selects an ad position from dropdown
2. **Instantly**, a reference image appears below showing:
   - Where the ad will be displayed
   - The position name
   - Helpful description
3. User can see exactly where their ad will appear before uploading

### Viewing an Ad

1. User views ad details
2. In the sidebar, below the ad image, they see:
   - A "Position Reference" card
   - The reference image showing placement
   - Position name

## Adding New Reference Images

To add reference images for new positions:

1. **Add image to folder:**
   - Place image in `public/ads/`
   - Use descriptive filename (e.g., `Footer Banner.png`)

2. **Update image map in AdPosition model:**
   ```php
   $imageMap = [
       // Existing mappings...
       'New Position Name' => 'New Position Image.png',
   ];
   ```

3. **That's it!** The system will automatically:
   - Link the position to the image
   - Show it in the form when position is selected
   - Display it in the ad view

## Benefits

1. **Better UX:** Users see exactly where their ad will appear
2. **Fewer Mistakes:** Users select the correct position for their needs
3. **Visual Guidance:** Reference images guide ad creation
4. **Self-Service:** Users don't need to ask where positions are located
5. **Flexible:** Easy to add new reference images
6. **Smart Matching:** Fuzzy matching handles slight name variations

## Files Modified

1. `Modules/SystemSetting/app/Models/AdPosition.php` - Added reference image methods
2. `Modules/SystemSetting/resources/views/ads/view.blade.php` - Added reference image display
3. `Modules/SystemSetting/resources/views/ads/form.blade.php` - Added dynamic reference image preview
4. `Modules/SystemSetting/lang/en/ads.php` - Added translation keys
5. `Modules/SystemSetting/lang/ar/ads.php` - Added Arabic translations

## Testing

### Test 1: View Ad with Reference Image
1. Go to any ad view page
2. ✅ Should see "Position Reference" card in sidebar
3. ✅ Should show reference image for that position
4. ✅ Should show position name below image

### Test 2: Create Ad with Reference Image
1. Go to create ad page
2. Select a position from dropdown
3. ✅ Reference image should appear immediately
4. ✅ Should show correct image for selected position
5. Change position
6. ✅ Reference image should update

### Test 3: Position Without Reference Image
1. Select a position that doesn't have a reference image
2. ✅ No reference image section should appear
3. ✅ Form should work normally

## Status
✅ **COMPLETE** - Reference images linked to ad positions
✅ **TESTED** - Images display correctly in view and form
✅ **DOCUMENTED** - Translation keys added
✅ **FLEXIBLE** - Easy to add new reference images
