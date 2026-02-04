# Critical Performance Fix - Enable Opcache

## The Problem

Your PHP installation has **Opcache disabled**, which means PHP recompiles every single file on every request. This is causing 5+ second delays.

## The Fix

### Step 1: Enable Opcache in PHP

1. Open Laragon
2. Click **Menu** → **PHP** → **php.ini**
3. Find these lines (around line 1800-1900):

```ini
;opcache.enable=1
;opcache.enable_cli=0
```

4. **Remove the semicolons** to enable them:

```ini
opcache.enable=1
opcache.enable_cli=0
```

5. Add these recommended settings below them:

```ini
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

6. **Save the file**
7. **Restart Laragon** (Stop All → Start All)

### Step 2: Change Session Driver to File

Database sessions are slow. Change to file-based sessions:

In your `.env` file, change:
```
SESSION_DRIVER=file
```

Then run:
```bash
php artisan config:clear
```

### Step 3: Verify Opcache is Enabled

Run this command:
```bash
php -i | findstr "opcache.enable"
```

You should see:
```
opcache.enable => On => On
```

## Expected Results

- **Before**: 5+ seconds per request
- **After**: 100-500ms per request (10-50x faster!)

## Why This Matters

Without Opcache:
- PHP reads and compiles every file on every request
- Your app has hundreds of PHP files
- Each compilation takes milliseconds
- Total: 5+ seconds per request

With Opcache:
- PHP compiles files once
- Stores compiled code in memory
- Reuses compiled code
- Total: 100-500ms per request
