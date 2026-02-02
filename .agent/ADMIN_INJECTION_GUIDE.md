# Admin Injection Guide

## Overview

You can now inject admin users from the old system into your new system.

---

## How to Use

### Inject Admins

```
http://127.0.0.1:8000/en/eg/admin/inject-data?include=admins
```

**Note:** No need for `truncate=1` because we don't want to delete existing admins.

---

## What It Does

### 1. Checks for Existing Users
- Searches by **email** (more reliable than ID)
- If user exists: **Updates** their information
- If user doesn't exist: **Creates** new user

### 2. Sets User Type
- Sets `user_type_id` to admin type (ID: 1)

### 3. Assigns Admin Role
- Automatically attaches the "admin" role to the user
- Uses your existing admin role from the database

### 4. Sets Default Password
- New admins get password: `password123`
- **Important:** Tell admins to change their password after first login!

### 5. Preserves Data
- Name, email, phone
- Gender, birth date, address
- Social media links (Facebook, Twitter, Instagram, Website)
- Profile image (if exists)
- Created/updated timestamps

---

## Response Example

```json
{
  "status": true,
  "message": "Data injected successfully",
  "total_fetched": 16,
  "pages_processed": 2,
  "last_page": 2,
  "result": {
    "type": "admins",
    "injected": 10,
    "updated": 6,
    "skipped": 0,
    "errors": [],
    "note": "New admins created with default password: password123"
  }
}
```

---

## Important Notes

### Default Password
All newly created admins will have the password: **`password123`**

**Security Recommendation:**
1. After injection, send email to all new admins
2. Ask them to change their password immediately
3. Or use password reset functionality

### Admin Role Required
The injection requires an "admin" role to exist in your database.

**Check if admin role exists:**
```sql
SELECT * FROM roles WHERE name = 'admin' OR name = 'Admin';
```

**If not found, create it:**
```sql
INSERT INTO roles (name, created_at, updated_at) 
VALUES ('admin', NOW(), NOW());
```

### User Type
The script assumes `user_type_id = 1` is for admins.

**Verify:**
```sql
SELECT * FROM user_types WHERE id = 1;
```

---

## Testing

### 1. Run Injection
```
http://127.0.0.1:8000/en/eg/admin/inject-data?include=admins
```

### 2. Check Logs
```bash
tail -f storage/logs/laravel.log | grep "Admin"
```

### 3. Verify in Database
```sql
-- Check created admins
SELECT id, name, email, user_type_id 
FROM users 
WHERE user_type_id = 1;

-- Check role assignments
SELECT u.name, u.email, r.name as role_name
FROM users u
JOIN user_role ur ON u.id = ur.user_id
JOIN roles r ON ur.role_id = r.id
WHERE u.user_type_id = 1;
```

### 4. Test Login
Try logging in with:
- Email: One of the injected admin emails
- Password: `password123`

---

## Troubleshooting

### Error: "Admin role not found"

**Solution:** Create admin role
```sql
INSERT INTO roles (name, created_at, updated_at) 
VALUES ('admin', NOW(), NOW());
```

### Error: "Duplicate email"

This is normal if the admin already exists. The script will **update** the existing user instead of creating a new one.

### Error: "User type not found"

**Solution:** Check user types table
```sql
SELECT * FROM user_types;
```

Make sure ID 1 exists and is for admins.

---

## Data Mapping

| Old System Field | New System Field | Notes |
|-----------------|------------------|-------|
| id | id | Preserved if creating new user |
| name | name | Full name |
| email | email | Used for duplicate checking |
| phone | phone | Contact number |
| image | attachments | Profile image |
| gender | gender | male/female |
| birth_date | birth_date | Date of birth |
| address | address | Physical address |
| facebook | facebook | Facebook profile URL |
| twitter | twitter | Twitter profile URL |
| instagram | instagram | Instagram profile URL |
| website | website | Personal website URL |
| status | - | Not mapped (all active by default) |
| vendor_id | - | Not mapped (admins don't have vendors) |

---

## Security Considerations

### 1. Change Default Passwords
After injection, all new admins should change their password from `password123`.

### 2. Email Verification
Consider sending verification emails to new admins.

### 3. Two-Factor Authentication
Enable 2FA for admin accounts if available.

### 4. Audit Log
Check who was created/updated:
```bash
grep "Admin CREATED\|Admin UPDATED" storage/logs/laravel.log
```

---

## Batch Processing

If you have many admins (100+), process in batches:

```bash
# Page 1
http://127.0.0.1:8000/en/eg/admin/inject-data?include=admins&page=1&limit_pages=1

# Page 2
http://127.0.0.1:8000/en/eg/admin/inject-data?include=admins&page=2&limit_pages=1
```

---

## Summary

✅ **What's Injected:**
- Admin users with all their data
- Admin role assignment
- Profile images
- Social media links

✅ **Safe to Run Multiple Times:**
- Updates existing users by email
- Doesn't create duplicates
- Preserves existing data

⚠️ **Remember:**
- Default password is `password123`
- Tell admins to change their password
- Verify admin role exists before injection

---

**Status:** ✅ **READY TO USE**  
**Endpoint:** `/admin/inject-data?include=admins`  
**Default Password:** `password123`
