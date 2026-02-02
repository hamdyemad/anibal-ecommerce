# Performance Optimization Guide - Slow Application Fix

## Current Issues Identified

1. ✅ **Telescope Enabled in Local** - Logs everything, slows down requests
2. ✅ **Debug Mode Enabled** - `APP_DEBUG=true` adds overhead
3. ✅ **Route Cache Conflict** - Duplicate route names prevent caching
4. ⚠️ **No Opcache** - PHP code recompiled on every request
5. ⚠️ **Query Logging** - May be enabled globally

---

## Quick Fixes (Apply Now)

### 1. Disable Telescope in Development (Temporarily)

**Option A: Comment out in config/app.php**
```php
// App\Providers\TelescopeServiceProvider::class,
```

**Option B: Disable in .env**
Add to `.env`:
```env
TELESCOPE_ENABLED=false
```

Then update `TelescopeServiceProvider.php`:
```php
public function register(): void
{
    if (!config('telescope.enabled', true)) {
        Telescope::stopRecording();
        return;
    }
    // ... rest of code
}
```

### 2. Fix Route Cache Conflict

You have duplicate route names. Find and fix:
```bash
grep -r "admin.vendors.show" routes/
```

One route is named `admin.vendors.show` twice. Rename one of them.

### 3. Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 4. Enable Opcache

**Check if enabled:**
```bash
php -i | grep opcache
```

**Enable in php.ini** (`C:\laragon\bin\php\php-8.3.16\php.ini`):
```ini
[opcache]
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=1
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

**Restart Apache/Nginx after changing php.ini**

---

## Performance Optimization Checklist

### Level 1: Immediate (Do Now)

- [ ] **Disable Telescope** (biggest impact)
  ```bash
  # Comment out in config/app.php
  // App\Providers\TelescopeServiceProvider::class,
  ```

- [ ] **Fix Route Duplicate Names**
  ```bash
  # Find duplicates
  php artisan route:list | grep "admin.vendors.show"
  ```

- [ ] **Enable Opcache** (see above)

- [ ] **Cache Configuration**
  ```bash
  php artisan config:cache
  ```

- [ ] **Cache Views**
  ```bash
  php artisan view:cache
  ```

### Level 2: Database Optimization

- [ ] **Add Database Indexes**
  
  Check slow queries:
  ```sql
  -- Enable slow query log
  SET GLOBAL slow_query_log = 'ON';
  SET GLOBAL long_query_time = 1;
  ```

- [ ] **Optimize Queries with Eager Loading**
  
  Bad (N+1 problem):
  ```php
  $products = Product::all();
  foreach ($products as $product) {
      echo $product->vendor->name; // Queries vendor each time
  }
  ```
  
  Good:
  ```php
  $products = Product::with('vendor')->all();
  foreach ($products as $product) {
      echo $product->vendor->name; // No extra queries
  }
  ```

- [ ] **Use Query Caching**
  
  Already implemented in your repositories! ✅

### Level 3: Redis Optimization

- [ ] **Verify Redis is Running**
  ```bash
  redis-cli ping
  # Should return: PONG
  ```

- [ ] **Check Redis Memory**
  ```bash
  redis-cli info memory
  ```

- [ ] **Optimize Redis Config**
  
  In `.env`:
  ```env
  REDIS_CLIENT=predis
  REDIS_HOST=127.0.0.1
  REDIS_PORT=6379
  REDIS_CACHE_DB=1  # Separate database for cache
  ```

### Level 4: Asset Optimization

- [ ] **Compile Assets for Production**
  ```bash
  npm run build
  # or
  npm run prod
  ```

- [ ] **Enable Asset Versioning**
  
  In `vite.config.js`:
  ```js
  export default defineConfig({
      build: {
          manifest: true,
          rollupOptions: {
              output: {
                  manualChunks: {
                      vendor: ['jquery', 'bootstrap'],
                  }
              }
          }
      }
  });
  ```

### Level 5: Laravel Optimization

- [ ] **Optimize Autoloader**
  ```bash
  composer dump-autoload -o
  ```

- [ ] **Cache Routes** (after fixing duplicates)
  ```bash
  php artisan route:cache
  ```

- [ ] **Optimize Config**
  ```bash
  php artisan config:cache
  ```

- [ ] **Optimize Events**
  ```bash
  php artisan event:cache
  ```

---

## Monitoring Performance

### 1. Enable Laravel Debugbar (Development Only)

```bash
composer require barryvdh/laravel-debugbar --dev
```

Shows:
- Query count and time
- Memory usage
- View rendering time
- Route information

### 2. Check Slow Queries

Add to `AppServiceProvider.php`:
```php
use Illuminate\Support\Facades\DB;

public function boot()
{
    if (app()->environment('local')) {
        DB::listen(function ($query) {
            if ($query->time > 100) { // Queries taking > 100ms
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms'
                ]);
            }
        });
    }
}
```

### 3. Profile with Telescope (When Needed)

Only enable Telescope when debugging:
```php
// In specific controller method
\Laravel\Telescope\Telescope::startRecording();
// ... your code
\Laravel\Telescope\Telescope::stopRecording();
```

---

## Common Performance Issues

### Issue 1: N+1 Query Problem

**Symptom:** Page loads slowly, many database queries

**Solution:** Use eager loading
```php
// Bad
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->customer->name; // N+1 queries
}

// Good
$orders = Order::with('customer')->all();
```

### Issue 2: Telescope Overhead

**Symptom:** Every request is slow

**Solution:** Disable Telescope or use filters
```php
Telescope::filter(function (IncomingEntry $entry) {
    // Only record exceptions and failed requests
    return $entry->isReportableException() ||
           $entry->isFailedRequest();
});
```

### Issue 3: Missing Indexes

**Symptom:** Queries on large tables are slow

**Solution:** Add indexes
```php
Schema::table('products', function (Blueprint $table) {
    $table->index('vendor_id');
    $table->index('status');
    $table->index(['vendor_id', 'status']); // Composite index
});
```

### Issue 4: Large Result Sets

**Symptom:** Memory issues, slow pagination

**Solution:** Use chunking or cursor
```php
// Instead of
$products = Product::all(); // Loads all into memory

// Use
Product::chunk(100, function ($products) {
    foreach ($products as $product) {
        // Process
    }
});

// Or for large datasets
foreach (Product::cursor() as $product) {
    // Process one at a time
}
```

### Issue 5: Unoptimized Images

**Symptom:** Pages with images load slowly

**Solution:** 
- Optimize images before upload
- Use lazy loading
- Serve from CDN
- Use WebP format

---

## Production Optimization

When deploying to production:

```bash
# 1. Set environment
APP_ENV=production
APP_DEBUG=false

# 2. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Optimize autoloader
composer install --optimize-autoloader --no-dev

# 4. Compile assets
npm run build

# 5. Enable Opcache (in php.ini)
opcache.enable=1
opcache.validate_timestamps=0  # Don't check for file changes
```

---

## Benchmarking

### Before Optimization
```bash
# Test response time
curl -w "@curl-format.txt" -o /dev/null -s http://127.0.0.1:8000/admin/dashboard
```

Create `curl-format.txt`:
```
time_namelookup:  %{time_namelookup}\n
time_connect:  %{time_connect}\n
time_starttransfer:  %{time_starttransfer}\n
time_total:  %{time_total}\n
```

### After Optimization

Compare the times to see improvement.

---

## Quick Performance Test

Run this to see current performance:

```bash
# 1. Check Opcache status
php -i | grep opcache.enable

# 2. Check Redis connection
redis-cli ping

# 3. Count queries on a page
# Enable query log temporarily and check count

# 4. Check memory usage
php artisan tinker
>>> memory_get_usage(true) / 1024 / 1024 . 'MB'
```

---

## Recommended Settings for Development

**.env:**
```env
APP_ENV=local
APP_DEBUG=true  # Keep for debugging
TELESCOPE_ENABLED=false  # Disable for speed

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

DB_CONNECTION=mysql
```

**php.ini:**
```ini
memory_limit = 512M
max_execution_time = 60
opcache.enable = 1
opcache.validate_timestamps = 1
opcache.revalidate_freq = 2
```

---

## Summary

**Top 3 Performance Killers:**
1. 🔴 **Telescope** - Disable in development
2. 🔴 **No Opcache** - Enable in php.ini
3. 🔴 **N+1 Queries** - Use eager loading

**Quick Win Commands:**
```bash
# Disable Telescope (comment in config/app.php)
# Enable Opcache (edit php.ini)
# Clear caches
php artisan cache:clear
php artisan config:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload -o

# Restart server
# (Restart Apache/Nginx in Laragon)
```

**Expected Results:**
- Page load: 200-500ms (from 2-5 seconds)
- Memory usage: 50-100MB (from 200-300MB)
- Query count: 10-30 per page (from 100+)

---

**Status:** 📋 **ACTION REQUIRED**  
**Priority:** 🔴 **HIGH**  
**Impact:** Massive performance improvement (5-10x faster)
