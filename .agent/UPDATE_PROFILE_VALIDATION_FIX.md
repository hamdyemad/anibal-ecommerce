# Update Profile Validation Fix - COMPLETE ✅

**Status:** COMPLETE  
**Date:** February 2, 2026  
**Endpoint:** `POST /auth/update-profile`

---

## Overview
Fixed validation logic so `current_password` is only required when the user wants to change their password, not for general profile updates. Added avatar upload functionality.

---

## Changes Made

### 1. Updated Validation Rules ✅
**File:** `Modules/Customer/app/Http/Requests/Api/UpdateProfileRequest.php`

**Validation Rules:**
```php
'first_name' => 'sometimes|string|max:255',
'last_name' => 'sometimes|string|max:255',
'phone' => 'sometimes|string|max:20',
'lang' => 'sometimes|in:en,ar',
'avatar' => 'sometimes|image|mimes:jpeg,jpg,png,gif|max:2048',
'current_password' => 'required_with:new_password|string',
'new_password' => 'sometimes|string|min:8|confirmed',
```

**Key Changes:**
- `current_password` - Only required when `new_password` is provided
- `avatar` - New field for profile image upload (max 2MB, jpeg/jpg/png/gif)

### 2. Added Avatar Upload Functionality ✅
**Files Modified:**
- `Modules/Customer/app/Services/Api/CustomerApiService.php`
- `Modules/Customer/app/Repositories/Api/CustomerApiRepository.php`
- `Modules/Customer/app/Interfaces/Api/CustomerApiRepositoryInterface.php`

**Implementation:**
```php
public function updateAvatar(Customer $customer, $avatar): Customer
{
    DB::transaction(function () use ($customer, $avatar) {
        // Delete old avatar if exists
        if ($customer->image) {
            $oldImagePath = storage_path('app/public/' . $customer->image);
            if (file_exists($oldImagePath)) {
                @unlink($oldImagePath);
            }
        }

        // Store new avatar
        $path = $avatar->store('customers/avatars', 'public');
        $customer->update(['image' => $path]);
    });

    return $customer->fresh();
}
```

**Features:**
- Deletes old avatar before uploading new one
- Stores avatar in `storage/app/public/customers/avatars/`
- Returns full URL via `asset('storage/' . $customer->image)`
- Wrapped in database transaction for safety

### 3. Updated Service Logic ✅
**File:** `Modules/Customer/app/Services/Api/CustomerApiService.php`

```php
// Handle avatar upload if provided
if (isset($data['avatar']) && $data['avatar']) {
    $customer = $this->customerRepository->updateAvatar($customer, $data['avatar']);
}
```

---

## Usage Examples

### 1. Update Profile Info Only (No Password Required)
```bash
POST /auth/update-profile
Content-Type: application/json

{
  "first_name": "John",
  "last_name": "Doe",
  "phone": "01234567890",
  "lang": "en"
}
```
✅ **Works without `current_password`**

### 2. Update Avatar Only
```bash
POST /auth/update-profile
Content-Type: multipart/form-data

avatar: [image file]
```
✅ **Works without `current_password`**

### 3. Update Password (Current Password Required)
```bash
POST /auth/update-profile
Content-Type: application/json

{
  "current_password": "old_password",
  "new_password": "new_password123",
  "new_password_confirmation": "new_password123"
}
```
✅ **Requires `current_password`**

### 4. Update Everything Together
```bash
POST /auth/update-profile
Content-Type: multipart/form-data

first_name: John
last_name: Doe
phone: 01234567890
avatar: [image file]
current_password: old_password
new_password: new_password123
new_password_confirmation: new_password123
```
✅ **All fields updated together**

### 5. Invalid: New Password Without Current Password
```json
POST /auth/update-profile
{
  "new_password": "new_password123",
  "new_password_confirmation": "new_password123"
}
```
❌ **Validation Error:** "Current password is required when changing password."

---

## Validation Rules Summary

| Field | Rule | Description |
|-------|------|-------------|
| `first_name` | `sometimes\|string\|max:255` | Optional, string, max 255 chars |
| `last_name` | `sometimes\|string\|max:255` | Optional, string, max 255 chars |
| `phone` | `sometimes\|string\|max:20` | Optional, string, max 20 chars |
| `lang` | `sometimes\|in:en,ar` | Optional, must be 'en' or 'ar' |
| `avatar` | `sometimes\|image\|mimes:jpeg,jpg,png,gif\|max:2048` | Optional, image file, max 2MB |
| `current_password` | `required_with:new_password\|string` | Required only when `new_password` is provided |
| `new_password` | `sometimes\|string\|min:8\|confirmed` | Optional, min 8 chars, must match confirmation |

---

## Avatar Upload Details

### Storage Location
- **Path:** `storage/app/public/customers/avatars/`
- **Public URL:** `https://yourdomain.com/storage/customers/avatars/filename.jpg`

### Supported Formats
- JPEG (.jpeg, .jpg)
- PNG (.png)
- GIF (.gif)

### File Size Limit
- Maximum: 2MB (2048 KB)

### Old Avatar Handling
- Automatically deleted when new avatar is uploaded
- Prevents storage bloat

### Response Format
```json
{
  "status": true,
  "message": "Profile updated successfully.",
  "data": {
    "id": 812,
    "full_name": "John Doe",
    "email": "user@example.com",
    "phone": "01234567890",
    "image": "https://yourdomain.com/storage/customers/avatars/abc123.jpg",
    "lang": "en",
    "gender": "male",
    "status": true,
    "verified": true,
    "created_at": "02 Feb, 2026, 11:29 AM",
    "updated_at": "02 Feb, 2026, 12:03 PM"
  }
}
```

---

## Security Features

1. **Password Verification:** Current password is verified before allowing password change
2. **Password Confirmation:** New password must be confirmed (`new_password_confirmation`)
3. **Minimum Length:** New password must be at least 8 characters
4. **Hash Check:** Uses `Hash::check()` to verify current password
5. **Exception Handling:** Throws `InvalidPasswordException` if current password is wrong
6. **File Validation:** Avatar must be valid image file (jpeg/jpg/png/gif)
7. **File Size Limit:** Avatar limited to 2MB to prevent abuse
8. **Transaction Safety:** Avatar upload wrapped in database transaction

---

## Files Modified
1. `Modules/Customer/app/Http/Requests/Api/UpdateProfileRequest.php`
   - Changed `current_password` validation from `required` to `required_with:new_password`
   - Added `avatar` validation rule
   - Added custom error messages

2. `Modules/Customer/app/Services/Api/CustomerApiService.php`
   - Moved password verification inside the password update block
   - Added avatar upload handling
   - Only checks current password when changing password

3. `Modules/Customer/app/Repositories/Api/CustomerApiRepository.php`
   - Added `updateAvatar()` method
   - Handles file storage and old file deletion

4. `Modules/Customer/app/Interfaces/Api/CustomerApiRepositoryInterface.php`
   - Added `updateAvatar()` method signature

---

## Testing Checklist
- [x] Can update profile info without providing password
- [x] Can upload avatar without providing password
- [x] Cannot change password without providing current password
- [x] Current password is verified before password change
- [x] New password requires confirmation
- [x] Avatar uploads successfully
- [x] Old avatar is deleted when new one is uploaded
- [x] Avatar URL is returned correctly in response
- [x] File validation works (type and size)
- [x] Custom error messages display correctly
- [x] All profile fields update correctly

---

## Status: COMPLETE ✅
Profile update validation and avatar upload functionality now work correctly:
- `current_password` is only required when changing password
- Avatar can be uploaded independently
- Old avatars are automatically cleaned up
