# Configuration Links - Usage Example

## Scenario
You have a "Color" variant key with a "Red" value that needs to be linked to multiple parent configurations (Nike Air, Ikea Bed, Wardrobe).

## Step-by-Step Guide

### 1. Create the Shared Configuration Value (Red)
First, create "Red" as a standalone configuration:

```
Navigate to: http://127.0.0.1:8000/en/eg/admin/variants-configurations/create

Fields:
- Key: Color
- Name (EN): Red
- Name (AR): أحمر
- Type: color
- Value: #FF0000
- Parent: None (leave empty)

Click Save
```

Let's say this creates a configuration with ID = 10

### 2. Create Parent Configurations

Create your parent configurations (Nike Air, Ikea Bed, Wardrobe):

```
Nike Air (ID: 1)
- Key: Model
- Name: Nike Air
- Parent: None

Ikea Bed (ID: 4)
- Key: Furniture
- Name: Ikea Bed
- Parent: None

Wardrobe (ID: 7)
- Key: Furniture
- Name: Wardrobe
- Parent: None
```

### 3. Link Red to Multiple Parents

#### Option A: Using the UI

1. Go to Nike Air detail page:
   `http://127.0.0.1:8000/en/eg/admin/variants-configurations/1`

2. Scroll to "Linked Children" section

3. Click "Manage Links" button

4. In the modal, select "Red" from the list

5. Click "Save"

6. Repeat for Ikea Bed (ID: 4) and Wardrobe (ID: 7)

#### Option B: Using API/AJAX

```javascript
// Link Red (ID: 10) to Nike Air (ID: 1)
$.ajax({
    url: '/en/eg/admin/variants-configurations/link-child',
    method: 'POST',
    data: {
        _token: 'YOUR_CSRF_TOKEN',
        parent_id: 1,
        child_id: 10
    },
    success: function(response) {
        console.log(response.message);
    }
});

// Link Red (ID: 10) to Ikea Bed (ID: 4)
$.ajax({
    url: '/en/eg/admin/variants-configurations/link-child',
    method: 'POST',
    data: {
        _token: 'YOUR_CSRF_TOKEN',
        parent_id: 4,
        child_id: 10
    }
});

// Link Red (ID: 10) to Wardrobe (ID: 7)
$.ajax({
    url: '/en/eg/admin/variants-configurations/link-child',
    method: 'POST',
    data: {
        _token: 'YOUR_CSRF_TOKEN',
        parent_id: 7,
        child_id: 10
    }
});
```

### 4. Verify the Links

Visit any parent configuration detail page and check the "Linked Children" section:
- `http://127.0.0.1:8000/en/eg/admin/variants-configurations/1` (Nike Air)
- `http://127.0.0.1:8000/en/eg/admin/variants-configurations/4` (Ikea Bed)
- `http://127.0.0.1:8000/en/eg/admin/variants-configurations/7` (Wardrobe)

You should see "Red" listed as a linked child for all three.

### 5. Update Red Once, Affects All

Now if you edit Red:
1. Go to `http://127.0.0.1:8000/en/eg/admin/variants-configurations/10/edit`
2. Change the name from "Red" to "Crimson"
3. Change the color value from #FF0000 to #DC143C
4. Save

All three parents (Nike Air, Ikea Bed, Wardrobe) will now show "Crimson" instead of "Red" - no duplication!

## API Endpoints Reference

### Link a Child
```
POST /en/eg/admin/variants-configurations/link-child
Body: { parent_id: 1, child_id: 10 }
```

### Unlink a Child
```
POST /en/eg/admin/variants-configurations/unlink-child
Body: { parent_id: 1, child_id: 10 }
```

### Sync All Links (Replace existing)
```
POST /en/eg/admin/variants-configurations/sync-linked-children
Body: { parent_id: 1, child_ids: [10, 11, 12] }
```

### Get Linked Children
```
GET /en/eg/admin/variants-configurations/1/linked-children
```

### Get All Children (Direct + Linked)
```
GET /en/eg/admin/variants-configurations/1/all-children
```

## Database Structure

After linking, your `variants_configurations_links` table will look like:

| id | parent_config_id | child_config_id | created_at | updated_at |
|----|------------------|-----------------|------------|------------|
| 1  | 1 (Nike Air)     | 10 (Red)        | ...        | ...        |
| 2  | 4 (Ikea Bed)     | 10 (Red)        | ...        | ...        |
| 3  | 7 (Wardrobe)     | 10 (Red)        | ...        | ...        |

Notice how Red (ID: 10) appears only once in `variants_configurations` table but is linked to three different parents!

## Benefits Demonstrated

1. **No Duplication**: Red exists once (ID: 10)
2. **Easy Updates**: Change Red once, affects all parents
3. **Flexible**: Can link/unlink at any time
4. **Backward Compatible**: Old parent_id relationships still work
5. **Clear UI**: Visual management through the detail page
