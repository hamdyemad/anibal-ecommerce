# Quick Access Guide - Configuration Links

## 🎯 Direct URLs

Replace `{id}` with any variant configuration ID:

### View Configuration with Links Feature:
```
http://127.0.0.1:8000/en/eg/admin/variants-configurations/{id}
```

### Examples:
```
http://127.0.0.1:8000/en/eg/admin/variants-configurations/1
http://127.0.0.1:8000/en/eg/admin/variants-configurations/2
http://127.0.0.1:8000/en/eg/admin/variants-configurations/3
```

## 📋 Menu Navigation

```
Dashboard
  └─ Products (في القائمة الجانبية)
      └─ Variant Configurations (تكوينات المتغيرات)
          └─ Click any row to view details
              └─ Scroll down to "Linked Children" section
```

## 🖱️ Click Path

```
Step 1: Open browser
   ↓
Step 2: Go to http://127.0.0.1:8000/en/eg/admin/variants-configurations
   ↓
Step 3: Click the "eye icon" 👁️ on any row
   ↓
Step 4: Scroll down past "Basic Information" and "Children" sections
   ↓
Step 5: Find "🔗 Linked Children" section
   ↓
Step 6: Click "Manage Links" button
   ↓
Step 7: Select configurations and click "Save"
```

## 🎬 Video-Style Instructions

### Adding a Link:

```
1. [CLICK] Variants Configurations menu item
   Screen shows: List of all configurations

2. [CLICK] Eye icon on "Red" configuration
   Screen shows: Red configuration details page

3. [SCROLL DOWN] to "Linked Children" section
   Screen shows: Empty or existing linked children

4. [CLICK] "Manage Links" button
   Screen shows: Modal with multi-select dropdown

5. [SELECT] "Nike Air" from dropdown (hold Ctrl for multiple)
   Screen shows: Selected items highlighted

6. [CLICK] "Save" button
   Screen shows: Success message + "Nike Air" badge appears

7. [DONE] Nike Air is now linked to Red!
```

### Removing a Link:

```
1. [GO TO] Red configuration details page
   Screen shows: Red configuration with linked children

2. [SCROLL DOWN] to "Linked Children" section
   Screen shows: [Nike Air ×] [Ikea Bed ×] badges

3. [CLICK] × button on "Nike Air" badge
   Screen shows: Confirmation dialog

4. [CONFIRM] the action
   Screen shows: Success message + badge disappears

5. [DONE] Nike Air is unlinked from Red!
```

## 🔧 Developer Access (API)

### Using Browser Console:

Open browser console (F12) and run:

```javascript
// Get current variant ID from URL
const variantId = window.location.pathname.split('/').pop();

// Fetch linked children
fetch(`/en/eg/admin/variants-configurations/${variantId}/linked-children`)
    .then(r => r.json())
    .then(data => {
        console.log('Linked Children:', data);
    });
```

### Using Postman:

```
GET http://127.0.0.1:8000/en/eg/admin/variants-configurations/1/linked-children
Headers:
  Accept: application/json
  Cookie: [your session cookie]
```

## 📱 Mobile/Tablet Access

The feature works on mobile devices too!

1. Open browser on mobile
2. Navigate to: `http://127.0.0.1:8000/en/eg/admin/variants-configurations`
3. Tap any configuration
4. Scroll to "Linked Children"
5. Tap "Manage Links"
6. Select from dropdown
7. Tap "Save"

## 🎨 Visual Markers

Look for these visual elements:

- **Section Title**: "Linked Children" or "الأطفال المرتبطة" (Arabic)
- **Icon**: 🔗 (link icon) next to the title
- **Button**: Blue "Manage Links" button on the right
- **Badges**: Blue rounded badges with × buttons
- **Modal**: Popup window with multi-select dropdown

## ⚡ Keyboard Shortcuts

When in the "Manage Links" modal:
- `Ctrl + Click` (Windows/Linux) or `Cmd + Click` (Mac): Select multiple items
- `Shift + Click`: Select range of items
- `Esc`: Close modal
- `Enter`: Save (when focused on Save button)

## 🔍 Troubleshooting

### "I don't see the Linked Children section"
✅ Make sure you're on the **show page** (URL ends with `/1`, `/2`, etc.)
❌ Not the edit page (URL ends with `/1/edit`)
❌ Not the list page (URL ends with `/variants-configurations`)

### "The Manage Links button doesn't work"
1. Check browser console (F12) for errors
2. Verify jQuery is loaded: Type `$` in console
3. Verify Bootstrap is loaded: Type `bootstrap` in console
4. Clear cache and refresh (Ctrl+Shift+R)

### "I get a 404 error"
1. Run: `php artisan route:clear`
2. Run: `php artisan cache:clear`
3. Verify routes: `php artisan route:list --name=variants-configurations`

## 📞 Support

If you still can't find it, check:
1. File exists: `Modules/CatalogManagement/resources/views/variants-config/show.blade.php`
2. Routes registered: Run `php artisan route:list | grep "linked-children"`
3. Permissions: Make sure you have `variants-configurations.show` permission

## ✅ Checklist

Before asking for help, verify:
- [ ] I'm on the show/detail page (not edit or list)
- [ ] I scrolled down past "Basic Information" section
- [ ] I scrolled down past "Children" section
- [ ] I'm looking for "Linked Children" (not "Children")
- [ ] I cleared my browser cache
- [ ] I checked browser console for errors
- [ ] The migration has been run
- [ ] Routes are registered

---

**TL;DR**: Go to any variant configuration detail page and scroll down to find the "Linked Children" section with a "Manage Links" button!
