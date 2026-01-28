# CacheService Usage Guide

## Overview
Global CacheService that you can inject in any Repository or Service.

## Basic Usage in Repository

### Example 1: Simple Repository with Cache

```php
<?php

namespace App\Repositories;

use App\Services\CacheService;
use Modules\AreaSettings\app\Models\Country;

class CountryRepository
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get all countries (with cache)
     */
    public function all()
    {
        return $this->cache->remember(
            'countries:all',
            fn() => Country::all(),
            300 // 5 minutes
        );
    }

    /**
     * Find country by ID (with cache)
     */
    public function find($id)
    {
        $key = $this->cache->key('Country', 'find', ['id' => $id]);
        
        return $this->cache->remember(
            $key,
            fn() => Country::find($id),
            600 // 10 minutes
        );
    }

    /**
     * Clear all country cache
     */
    public function clearCache()
    {
        $this->cache->forgetByPattern('countries:*');
    }
}
```

### Example 2: Repository with Search

```php
<?php

namespace Modules\CatalogManagement\app\Repositories;

use App\Services\CacheService;
use Modules\CatalogManagement\app\Models\Product;

class ProductRepository
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get products with filters (with cache)
     */
    public function search(array $filters)
    {
        $key = $this->cache->key('Product', 'search', $filters);
        
        return $this->cache->remember($key, function() use ($filters) {
            $query = Product::query();
            
            if (isset($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }
            
            if (isset($filters['search'])) {
                $query->where('name', 'like', "%{$filters['search']}%");
            }
            
            return $query->paginate($filters['per_page'] ?? 15);
        }, 180); // 3 minutes
    }

    /**
     * Clear product cache
     */
    public function clearCache()
    {
        $this->cache->forgetByPattern('product:*');
    }
}
```

### Example 3: Using in Controller

```php
<?php

namespace Modules\AreaSettings\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CountryRepository;

class CountryApiController extends Controller
{
    protected CountryRepository $countryRepo;

    public function __construct(CountryRepository $countryRepo)
    {
        $this->countryRepo = $countryRepo;
    }

    public function index()
    {
        $countries = $this->countryRepo->all();
        return response()->json($countries);
    }

    public function show($id)
    {
        $country = $this->countryRepo->find($id);
        return response()->json($country);
    }
}
```

## CacheService Methods

### 1. remember()
Get from cache or execute callback and cache result
```php
$data = $cache->remember('key', fn() => Model::all(), 300);
```

### 2. get()
Get data from cache
```php
$data = $cache->get('key', 'default_value');
```

### 3. put()
Store data in cache
```php
$cache->put('key', $data, 300);
```

### 4. has()
Check if key exists
```php
if ($cache->has('key')) {
    // ...
}
```

### 5. forget()
Remove single key
```php
$cache->forget('countries:all');
```

### 6. forgetByPattern()
Remove multiple keys by pattern (Redis only)
```php
$cache->forgetByPattern('countries:*');
$cache->forgetByPattern('product:search:*');
```

### 7. key()
Generate cache key
```php
$key = $cache->key('Country', 'find', ['id' => 1]);
// Result: "country:find:c4ca4238a0b923820dcc509a6f75849b"
```

### 8. flush()
Clear all cache
```php
$cache->flush();
```

## Cache Invalidation

### Option 1: Manual in Controller
```php
public function update(Request $request, $id)
{
    $country = Country::find($id);
    $country->update($request->all());
    
    // Clear cache
    $this->countryRepo->clearCache();
    
    return response()->json($country);
}
```

### Option 2: Using Model Observer
```php
// In CountryObserver
public function updated(Country $country)
{
    app(CacheService::class)->forgetByPattern('countries:*');
}
```

## Best Practices

### 1. Use Descriptive Keys
```php
// Good
'countries:all'
'countries:active'
'product:search:category_5'

// Bad
'data'
'list'
'cache1'
```

### 2. Set Appropriate TTL
```php
// Rarely changes - long TTL
$cache->remember('countries:all', fn() => Country::all(), 3600); // 1 hour

// Changes frequently - short TTL
$cache->remember('orders:pending', fn() => Order::pending(), 60); // 1 minute

// Real-time data - very short TTL
$cache->remember('dashboard:stats', fn() => $this->getStats(), 30); // 30 seconds
```

### 3. Clear Related Cache
```php
public function clearCache()
{
    // Clear all related patterns
    $this->cache->forgetByPattern('countries:*');
    $this->cache->forgetByPattern('regions:*'); // Related data
    $this->cache->forgetByPattern('cities:*');  // Related data
}
```

## Summary

✅ **Simple** - Just inject CacheService in constructor
✅ **Flexible** - Use in any Repository or Service
✅ **Clean** - All cache logic in one place
✅ **Powerful** - Pattern-based cache clearing with Redis
✅ **Easy** - Helper methods for common operations

No middleware, no complexity, just inject and use!
