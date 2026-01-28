# Cache Response Middleware - Usage Guide

## Overview

The `CacheResponse` middleware automatically caches GET request responses to improve API performance and reduce database load.

## Features

- ✅ Automatically caches successful GET requests
- ✅ Configurable TTL (Time To Live) per route
- ✅ User-aware caching (different cache for each user)
- ✅ Cache headers (X-Cache: HIT/MISS) for debugging
- ✅ Only caches JSON responses
- ✅ Skips caching for non-GET requests

---

## Installation

The middleware has been created and registered. It's ready to use!

**Files Created:**
- `app/Http/Middleware/CacheResponse.php` - The middleware
- `app/Http/Kernel.php` - Registered as `cache.response`

---

## Usage

### Basic Usage (60 seconds cache)

```php
// routes/api.php
Route::get('/products', [ProductController::class, 'index'])
    ->middleware('cache.response');
```

### Custom TTL (Time To Live)

```php
// Cache for 5 minutes (300 seconds)
Route::get('/products', [ProductController::class, 'index'])
    ->middleware('cache.response:300');

// Cache for 1 hour (3600 seconds)
Route::get('/categories', [CategoryController::class, 'index'])
    ->middleware('cache.response:3600');

// Cache for 24 hours (86400 seconds)
Route::get('/static-data', [DataController::class, 'index'])
    ->middleware('cache.response:86400');
```

### Apply to Route Groups

```php
// Cache all routes in this group for 5 minutes
Route::middleware(['cache.response:300'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/brands', [BrandController::class, 'index']);
});
```

### Apply to Controller

```php
// app/Http/Controllers/Api/ProductController.php
class ProductController extends Controller
{
    public function __construct()
    {
        // Cache all GET methods for 5 minutes
        $this->middleware('cache.response:300')->only(['index', 'show']);
    }
    
    public function index()
    {
        // This will be cached
        return response()->json(Product::all());
    }
    
    public function show($id)
    {
        // This will be cached
        return response()->json(Product::find($id));
    }
    
    public function store(Request $request)
    {
        // This will NOT be cached (POST request)
        return response()->json(Product::create($request->all()));
    }
}
```

---

## Real-World Examples

### Example 1: Product Listing API

```php
// routes/api.php

// Cache product list for 10 minutes
Route::get('/api/products', [ProductController::class, 'index'])
    ->middleware('cache.response:600');

// Cache single product for 5 minutes
Route::get('/api/products/{id}', [ProductController::class, 'show'])
    ->middleware('cache.response:300');
```

**Before Caching:**
- Request 1: Database query (500ms)
- Request 2: Database query (500ms)
- Request 3: Database query (500ms)
- Total: 1500ms for 3 requests

**After Caching:**
- Request 1: Database query (500ms) - Cache MISS
- Request 2: From cache (5ms) - Cache HIT
- Request 3: From cache (5ms) - Cache HIT
- Total: 510ms for 3 requests (3x faster!)

### Example 2: Static Data APIs

```php
// Cache static data for 24 hours
Route::middleware(['cache.response:86400'])->group(function () {
    Route::get('/api/countries', [CountryController::class, 'index']);
    Route::get('/api/cities', [CityController::class, 'index']);
    Route::get('/api/regions', [RegionController::class, 'index']);
    Route::get('/api/categories', [CategoryController::class, 'index']);
    Route::get('/api/brands', [BrandController::class, 'index']);
});
```

### Example 3: User-Specific Data

```php
// Cache user's orders for 2 minutes
Route::get('/api/my-orders', [OrderController::class, 'myOrders'])
    ->middleware(['auth:sanctum', 'cache.response:120']);

// Cache user's profile for 5 minutes
Route::get('/api/profile', [ProfileController::class, 'show'])
    ->middleware(['auth:sanctum', 'cache.response:300']);
```

**Note:** The middleware automatically includes user ID in the cache key, so each user gets their own cached response.

### Example 4: Search Results

```php
// Cache search results for 1 minute
Route::get('/api/search', [SearchController::class, 'search'])
    ->middleware('cache.response:60');
```

**Cache Key Includes Query Parameters:**
- `/api/search?q=laptop` → Different cache
- `/api/search?q=phone` → Different cache
- `/api/search?q=laptop&page=2` → Different cache

---

## How It Works

### 1. Cache Key Generation

The middleware generates a unique cache key based on:
- User ID (if authenticated) or "guest"
- Full URL including query parameters

```php
// Example cache keys:
'cache:response:abc123...' // For /api/products
'cache:response:def456...' // For /api/products?page=2
'cache:response:ghi789...' // For /api/products (different user)
```

### 2. Cache Flow

```
┌─────────────────┐
│  GET Request    │
└────────┬────────┘
         │
         ↓
    ┌────────────┐
    │ Cache Key  │
    │ Generated  │
    └────┬───────┘
         │
         ↓
    ┌────────────┐      YES     ┌──────────────┐
    │ Cache      │─────────────→│ Return Cache │
    │ Exists?    │              │ (X-Cache:HIT)│
    └────┬───────┘              └──────────────┘
         │ NO
         ↓
    ┌────────────┐
    │ Process    │
    │ Request    │
    └────┬───────┘
         │
         ↓
    ┌────────────┐
    │ Store in   │
    │ Cache      │
    │(X-Cache:   │
    │ MISS)      │
    └────┬───────┘
         │
         ↓
    ┌────────────┐
    │ Return     │
    │ Response   │
    └────────────┘
```

### 3. Cache Headers

The middleware adds headers to help with debugging:

```http
HTTP/1.1 200 OK
X-Cache: HIT
X-Cache-Key: cache:response:abc123...
Content-Type: application/json

{
  "data": [...]
}
```

- `X-Cache: HIT` - Response served from cache
- `X-Cache: MISS` - Response generated and cached
- `X-Cache-Key` - The cache key used

---

## Cache Invalidation

### Manual Cache Clearing

```php
// Clear specific cache key
Cache::forget('cache:response:' . sha1('guest:/api/products'));

// Clear all response caches
Cache::flush(); // Clears ALL cache

// Clear cache with pattern (if using Redis)
$keys = Cache::getRedis()->keys('cache:response:*');
foreach ($keys as $key) {
    Cache::forget($key);
}
```

### Automatic Cache Invalidation

Create a helper to clear cache when data changes:

```php
// app/Helpers/CacheHelper.php
<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * Clear response cache for a specific URL pattern
     */
    public static function clearResponseCache(string $urlPattern)
    {
        // For Redis
        if (config('cache.default') === 'redis') {
            $pattern = 'cache:response:*' . sha1($urlPattern) . '*';
            $keys = Cache::getRedis()->keys($pattern);
            
            foreach ($keys as $key) {
                Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
            }
        } else {
            // For file/database cache, you'll need to clear all
            Cache::flush();
        }
    }
}
```

Use in your controllers:

```php
// app/Http/Controllers/ProductController.php
public function store(Request $request)
{
    $product = Product::create($request->all());
    
    // Clear product list cache
    CacheHelper::clearResponseCache('/api/products');
    
    return response()->json($product);
}

public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $product->update($request->all());
    
    // Clear product list and detail cache
    CacheHelper::clearResponseCache('/api/products');
    CacheHelper::clearResponseCache('/api/products/' . $id);
    
    return response()->json($product);
}
```

---

## Best Practices

### 1. Choose Appropriate TTL

```php
// Static data - Long TTL (24 hours)
Route::get('/api/countries', [CountryController::class, 'index'])
    ->middleware('cache.response:86400');

// Frequently updated - Short TTL (1-5 minutes)
Route::get('/api/products', [ProductController::class, 'index'])
    ->middleware('cache.response:300');

// Real-time data - Very short TTL (30 seconds)
Route::get('/api/live-prices', [PriceController::class, 'index'])
    ->middleware('cache.response:30');

// Don't cache - No middleware
Route::get('/api/user-notifications', [NotificationController::class, 'index']);
```

### 2. Don't Cache Everything

**DO Cache:**
- ✅ Product listings
- ✅ Category lists
- ✅ Static data (countries, cities)
- ✅ Search results
- ✅ Public profiles
- ✅ Reports (with longer TTL)

**DON'T Cache:**
- ❌ User authentication endpoints
- ❌ Real-time notifications
- ❌ Shopping cart
- ❌ Checkout process
- ❌ Admin actions
- ❌ File uploads

### 3. Monitor Cache Performance

```php
// Add logging to track cache hits/misses
Log::info('Cache HIT', ['key' => $key, 'url' => $request->fullUrl()]);
Log::info('Cache MISS', ['key' => $key, 'url' => $request->fullUrl()]);
```

### 4. Test Cache Behavior

```bash
# First request (cache MISS)
curl -i http://127.0.0.1:8000/api/products
# Check: X-Cache: MISS

# Second request (cache HIT)
curl -i http://127.0.0.1:8000/api/products
# Check: X-Cache: HIT
```

---

## Performance Impact

### Example: Product API

**Without Cache:**
```
100 requests × 500ms = 50,000ms (50 seconds)
Database queries: 100
```

**With Cache (5 min TTL):**
```
1 request × 500ms (MISS) = 500ms
99 requests × 5ms (HIT) = 495ms
Total: 995ms (~1 second)
Database queries: 1

Performance improvement: 50x faster!
```

---

## Troubleshooting

### Issue 1: Cache Not Working

**Check:**
1. Is Redis running? `redis-cli ping`
2. Is cache driver set? Check `.env`: `CACHE_DRIVER=redis`
3. Is middleware applied? Check route definition
4. Is request method GET? Only GET requests are cached

### Issue 2: Stale Data

**Solution:**
- Reduce TTL
- Clear cache after updates
- Use cache tags (Redis only)

### Issue 3: Different Users See Same Data

**Check:**
- Middleware includes user ID in cache key
- Ensure authentication middleware runs before cache middleware

```php
// Correct order
Route::get('/api/my-orders', [OrderController::class, 'myOrders'])
    ->middleware(['auth:sanctum', 'cache.response:120']);
```

---

## Advanced Usage

### Custom Cache Key

Modify the `key()` method in the middleware:

```php
protected function key(Request $request): string
{
    // Include additional parameters
    $userId = auth()->check() ? auth()->id() : 'guest';
    $locale = app()->getLocale();
    $country = session('country_code', 'default');
    $url = $request->fullUrl();
    
    return 'cache:response:' . sha1($userId . ':' . $locale . ':' . $country . ':' . $url);
}
```

### Conditional Caching

```php
public function handle(Request $request, Closure $next, $ttl = 60)
{
    // Don't cache if user is admin
    if (auth()->check() && auth()->user()->isAdmin()) {
        return $next($request);
    }
    
    // Don't cache if debugging
    if (config('app.debug')) {
        return $next($request);
    }
    
    // Continue with normal caching logic...
}
```

---

## Summary

✅ **Created**: `CacheResponse` middleware
✅ **Registered**: Available as `cache.response`
✅ **Usage**: Add to routes with optional TTL parameter
✅ **Benefits**: Faster responses, reduced database load
✅ **Automatic**: Works with existing code, no changes needed

**Next Steps:**
1. Apply middleware to your API routes
2. Test with `X-Cache` headers
3. Monitor performance improvements
4. Adjust TTL values based on data freshness needs
