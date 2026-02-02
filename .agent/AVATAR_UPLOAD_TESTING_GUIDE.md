# Avatar Upload Testing Guide

**Endpoint:** `POST /auth/update-profile`  
**Status:** Ready for Testing

---

## How to Test Avatar Upload

### Using Postman

1. **Set Request Type:** POST
2. **URL:** `http://127.0.0.1:8000/api/auth/update-profile`
3. **Headers:**
   - `Authorization: Bearer {your_access_token}`
   - `Accept: application/json`
4. **Body:** Select `form-data` (NOT raw JSON)
5. **Add Fields:**
   - Key: `avatar`, Type: `File`, Value: Select an image file
   - Key: `first_name`, Type: `Text`, Value: `John` (optional)
   - Key: `last_name`, Type: `Text`, Value: `Doe` (optional)

### Using cURL

```bash
curl -X POST http://127.0.0.1:8000/api/auth/update-profile \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -F "avatar=@/path/to/image.jpg" \
  -F "first_name=John" \
  -F "last_name=Doe"
```

---

## Expected Response

### Success Response
```json
{
  "status": true,
  "message": "Profile updated successfully.",
  "errors": [],
  "data": {
    "id": 812,
    "full_name": "John Doe",
    "email": "testuser@testuser.com",
    "phone": "0456451321",
    "image": "http://127.0.0.1:8000/storage/customers/avatars/abc123xyz.jpg",
    "lang": "en",
    "gender": "male",
    "status": true,
    "verified": true,
    "created_at": "02 Feb, 2026, 11:29 AM",
    "updated_at": "02 Feb, 2026, 12:30 PM"
  }
}
```

**Note:** The `image` field should now contain a full URL to the uploaded avatar.

---

## Test Cases

### Test 1: Upload Avatar Only ✅
```bash
POST /auth/update-profile
Body (form-data):
  avatar: [image file]
```
**Expected:** Avatar uploaded, image URL returned

### Test 2: Upload Avatar + Update Name ✅
```bash
POST /auth/update-profile
Body (form-data):
  avatar: [image file]
  first_name: John
  last_name: Doe
```
**Expected:** Avatar uploaded, name updated, both reflected in response

### Test 3: Replace Existing Avatar ✅
```bash
# First upload
POST /auth/update-profile
Body: avatar: [image1.jpg]

# Second upload (should delete image1.jpg)
POST /auth/update-profile
Body: avatar: [image2.jpg]
```
**Expected:** Old avatar deleted, new avatar uploaded

### Test 4: Invalid File Type ❌
```bash
POST /auth/update-profile
Body (form-data):
  avatar: [document.pdf]
```
**Expected Error:**
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "avatar": ["The avatar must be a file of type: jpeg, jpg, png, gif."]
  }
}
```

### Test 5: File Too Large ❌
```bash
POST /auth/update-profile
Body (form-data):
  avatar: [large_image.jpg] (> 2MB)
```
**Expected Error:**
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "avatar": ["The avatar must not be larger than 2MB."]
  }
}
```

---

## Verification Steps

### 1. Check Database
```sql
SELECT id, first_name, last_name, email, image 
FROM customers 
WHERE id = 812;
```
**Expected:** `image` column should contain path like `customers/avatars/abc123xyz.jpg`

### 2. Check File System
```bash
# Windows
dir storage\app\public\customers\avatars

# Linux/Mac
ls -la storage/app/public/customers/avatars
```
**Expected:** Image file should exist in this directory

### 3. Check Public Access
Open in browser:
```
http://127.0.0.1:8000/storage/customers/avatars/abc123xyz.jpg
```
**Expected:** Image should display

### 4. Check Old Avatar Deletion
After uploading a second avatar, verify the first one is deleted:
```bash
# The old file should NOT exist
dir storage\app\public\customers\avatars
```

---

## Troubleshooting

### Issue: "image" field returns empty string
**Cause:** Avatar not uploaded or validation failed  
**Solution:** Check request is `multipart/form-data`, not JSON

### Issue: Image URL returns 404
**Cause:** Storage link not created  
**Solution:** Run:
```bash
php artisan storage:link
```

### Issue: Old avatar not deleted
**Cause:** File permissions or path issue  
**Solution:** Check storage folder permissions:
```bash
chmod -R 775 storage/app/public/customers
```

### Issue: "The avatar field is required"
**Cause:** Using wrong validation rule  
**Solution:** Verify validation uses `sometimes`, not `required`

---

## Important Notes

1. **Content-Type:** Must use `multipart/form-data` for file uploads, NOT `application/json`
2. **Storage Link:** Ensure `php artisan storage:link` has been run
3. **Permissions:** Storage folder must be writable (775 or 777)
4. **File Size:** Maximum 2MB (2048 KB)
5. **Formats:** Only jpeg, jpg, png, gif allowed
6. **Old Files:** Automatically deleted when new avatar uploaded

---

## Status: Ready for Testing ✅
Avatar upload functionality is fully implemented and ready to test.
