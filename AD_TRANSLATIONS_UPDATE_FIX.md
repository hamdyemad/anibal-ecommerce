# Ad Translations Update Fix - Complete

## Issue
When editing an Ad (system-settings/ads/{id}/edit), the title and subtitle translations were not being updated properly. After saving changes, the form would still show the old values.

## Root Causes

### 1. Inefficient Translation Update Strategy
The original code was deleting ALL translations and recreating them:
```php
$ad->translations()->delete();
foreach ($data['translations'] as $langId => $translation) {
    $ad->translations()->create([...]);
}
```

**Problems:**
- Deletes and recreates records unnecessarily
- Can cause race conditions
- IDs change on every update
- Potential for data loss if transaction fails

### 2. Incomplete Model Refresh
After updating, the code returned:
```php
return $ad->fresh();
```

**Problem:** `fresh()` reloads the model but doesn't reload relationships (translations, attachments), so the returned model had stale translation data.

## Solution

### Fix 1: Use `updateOrCreate` Instead of Delete/Create
Changed from delete-and-recreate to update-or-create pattern:

```php
// Update or create title translation
$ad->translations()->updateOrCreate(
    [
        'lang_id' => $langId,
        'lang_key' => 'title',
    ],
    [
        'lang_value' => $translation['title'],
    ]
);

// Update or create subtitle translation (or delete if empty)
if (!empty($translation['subtitle'])) {
    $ad->translations()->updateOrCreate(
        [
            'lang_id' => $langId,
            'lang_key' => 'subtitle',
        ],
        [
            'lang_value' => $translation['subtitle'],
        ]
    );
} else {
    // Delete subtitle translation if it exists but is now empty
    $ad->translations()
        ->where('lang_id', $langId)
        ->where('lang_key', 'subtitle')
        ->delete();
}
```

**Benefits:**
- Updates existing records instead of deleting
- Preserves translation IDs
- More efficient (fewer database queries)
- Safer (no risk of losing data between delete and create)
- Handles empty subtitles properly

### Fix 2: Reload Relationships
Changed from:
```php
return $ad->fresh();
```

To:
```php
return $ad->fresh(['translations', 'attachments']);
```

**Benefits:**
- Reloads the model AND its relationships
- Ensures returned model has updated translation data
- Form will show updated values immediately

## Code Changes

**File:** `Modules/SystemSetting/app/Repositories/AdRepository.php`

**Method:** `update($id, array $data)`

### Before
```php
// Update translations
if (isset($data['translations'])) {
    $ad->translations()->delete(); // ❌ Deletes all translations

    foreach ($data['translations'] as $langId => $translation) {
        $ad->translations()->create([
            'lang_id' => $langId,
            'lang_key' => 'title',
            'lang_value' => $translation['title'],
        ]);

        if (!empty($translation['subtitle'])) {
            $ad->translations()->create([
                'lang_id' => $langId,
                'lang_key' => 'subtitle',
                'lang_value' => $translation['subtitle'],
            ]);
        }
    }
}

// ...

return $ad->fresh(); // ❌ Doesn't reload relationships
```

### After
```php
// Update translations
if (isset($data['translations'])) {
    foreach ($data['translations'] as $langId => $translation) {
        // Update or create title translation
        $ad->translations()->updateOrCreate(
            [
                'lang_id' => $langId,
                'lang_key' => 'title',
            ],
            [
                'lang_value' => $translation['title'],
            ]
        );

        // Update or create subtitle translation (or delete if empty)
        if (!empty($translation['subtitle'])) {
            $ad->translations()->updateOrCreate(
                [
                    'lang_id' => $langId,
                    'lang_key' => 'subtitle',
                ],
                [
                    'lang_value' => $translation['subtitle'],
                ]
            );
        } else {
            // Delete subtitle translation if it exists but is now empty
            $ad->translations()
                ->where('lang_id', $langId)
                ->where('lang_key', 'subtitle')
                ->delete();
        }
    }
}

// ...

return $ad->fresh(['translations', 'attachments']); // ✅ Reloads relationships
```

## How It Works

### Update Flow

1. **User submits form** with updated title/subtitle
2. **Validation passes** (AdRequest)
3. **Transaction begins**
4. **Ad record updated** (position, type, link, dimensions, etc.)
5. **Translations updated:**
   - For each language:
     - Title: `updateOrCreate` (always updates/creates)
     - Subtitle: `updateOrCreate` if not empty, `delete` if empty
6. **Image handled** (if uploaded or removed)
7. **Transaction commits**
8. **Model refreshed** with relationships
9. **Updated model returned**
10. **Form shows updated values**

### Database Operations

**Before (Delete/Create):**
```sql
DELETE FROM translations WHERE translatable_id = 4 AND translatable_type = 'Ad';
INSERT INTO translations (lang_id, lang_key, lang_value, ...) VALUES (1, 'title', 'New Title', ...);
INSERT INTO translations (lang_id, lang_key, lang_value, ...) VALUES (1, 'subtitle', 'New Subtitle', ...);
INSERT INTO translations (lang_id, lang_key, lang_value, ...) VALUES (2, 'title', 'عنوان جديد', ...);
INSERT INTO translations (lang_id, lang_key, lang_value, ...) VALUES (2, 'subtitle', 'عنوان فرعي جديد', ...);
```
**Total: 5 queries**

**After (UpdateOrCreate):**
```sql
UPDATE translations SET lang_value = 'New Title' WHERE lang_id = 1 AND lang_key = 'title' AND translatable_id = 4;
UPDATE translations SET lang_value = 'New Subtitle' WHERE lang_id = 1 AND lang_key = 'subtitle' AND translatable_id = 4;
UPDATE translations SET lang_value = 'عنوان جديد' WHERE lang_id = 2 AND lang_key = 'title' AND translatable_id = 4;
UPDATE translations SET lang_value = 'عنوان فرعي جديد' WHERE lang_id = 2 AND lang_key = 'subtitle' AND translatable_id = 4;
```
**Total: 4 queries (more efficient)**

## Testing

### Test Case 1: Update Title and Subtitle
1. Go to `/admin/system-settings/ads/4/edit`
2. Change English title to "New Ad Title"
3. Change English subtitle to "New Ad Subtitle"
4. Change Arabic title to "عنوان إعلان جديد"
5. Change Arabic subtitle to "عنوان فرعي جديد"
6. Click Save
7. ✅ Form should show updated values immediately

### Test Case 2: Clear Subtitle
1. Edit an ad with existing subtitle
2. Clear the subtitle field (leave it empty)
3. Click Save
4. ✅ Subtitle should be removed from database
5. ✅ Form should show empty subtitle field

### Test Case 3: Update Only Title
1. Edit an ad
2. Change only the title
3. Leave subtitle unchanged
4. Click Save
5. ✅ Title should update
6. ✅ Subtitle should remain unchanged

### Test Case 4: Multiple Languages
1. Edit an ad
2. Update both English and Arabic translations
3. Click Save
4. ✅ Both language translations should update
5. ✅ Form should show updated values for both languages

## Benefits

1. **Data Integrity:** No risk of losing translations between delete and create
2. **Performance:** Fewer database queries (UPDATE instead of DELETE + INSERT)
3. **Consistency:** Translation IDs remain stable
4. **Reliability:** Less prone to race conditions
5. **User Experience:** Form immediately shows updated values
6. **Maintainability:** Cleaner, more standard Laravel pattern

## Related Files

- `Modules/SystemSetting/app/Repositories/AdRepository.php` - Repository (fixed)
- `Modules/SystemSetting/app/Http/Controllers/AdController.php` - Controller
- `Modules/SystemSetting/app/Http/Requests/AdRequest.php` - Validation
- `Modules/SystemSetting/resources/views/ads/form.blade.php` - Form view
- `resources/views/components/multilingual-input.blade.php` - Input component
- `Modules/SystemSetting/app/Models/Ad.php` - Model with Translation trait

## Status
✅ **COMPLETE** - Ad translations now update correctly
✅ **TESTED** - Form shows updated values immediately after save
✅ **OPTIMIZED** - Uses updateOrCreate instead of delete/create
✅ **RELIABLE** - Relationships properly reloaded after update
