# Country Cache Testing Guide

## Understanding the Cache Scope

**Important:** The cache only applies to **API endpoints**, not admin panel requests.

### What Gets Cached
✅ **API Endpoint:** `GET /api/v1/area/countries`  
✅ **API Endpoint:** `GET /api/v1/area/countries/{id}`

### What Does NOT Get Cached
❌ **Admin Panel:** Country listing in admin  
❌ **Admin Panel:** Country edit/create forms  
❌ **Admin Panel:** DataTables requests

## Why You See `countryapi_keys: 0`

When you update a country in the admin panel:
1. ✅ Observer fires correctly
2. ✅ Tries to clear cache with pattern `countryapi:*`
3. ⚠️ Finds 0 keys because you haven't made any API requests yet
4. ✅ This is **NORMAL and EXPECTED**

## How to Test Properly

### Step 1: Make an API Request (Create Cache)
```bash
# Request 1: This will CREATE the cache
curl -X GET "http://localhost:8000/api/v1/area/countries?per_page=15" \
  -H "Accept: application/json"
```

**Expected Result:**
- Response time: ~100-200ms (queries database)
- Cache key created: `countryapi:all:paginated=true&per_page=15`
- No observer logs (just reading data)

### Step 2: Make Same Request Again (Use Cache)
```bash
# Request 2: This will USE the cache
curl -X GET "http://localhost:8000/api/v1/area/countries?per_page=15" \
  -H "Accept: application/json"
```

**Expected Result:**
- Response time: ~5-10ms (from cache)
- Same data returned
- No database query
- No observer logs

### Step 3: Update Country in Admin Panel
1. Go to admin panel
2. Edit any country
3. Save changes

**Expected Result in Logs:**
```
[2026-01-29 11:15:00] local.INFO: CountryObserver: Clearing country cache
[2026-01-29 11:15:00] local.INFO: CountryObserver: Cache cleared {"countryapi_keys":1}
```
**Note:** Now it shows `countryapi_keys: 1` (or more) because cache exists!

### Step 4: Make API Request Again (Cache Rebuilt)
```bash
# Request 3: This will REBUILD the cache with updated data
curl -X GET "http://localhost:8000/api/v1/area/countries?per_page=15" \
  -H "Accept: application/json"
```

**Expected Result:**
- Response time: ~100-200ms (queries database again)
- Returns UPDATED country data
- Cache key recreated
- No observer logs

## Verify Cache in Redis

### Check if Cache Exists
```bash
# List all country cache keys
redis-cli KEYS "countryapi:*"
```

**Before API Request:**
```
(empty array)
```

**After API Request:**
```
1) "countryapi:all:paginated=true&per_page=15"
```

### Check Cache Content
```bash
# View cached data
redis-cli GET "countryapi:all:paginated=true&per_page=15"
```

### Check Cache TTL
```bash
# Check time to live (should be ~3600 seconds = 1 hour)
redis-cli TTL "countryapi:all:paginated=true&per_page=15"
```

**Example Output:**
```
3598  # seconds remaining
```

### Monitor Cache Operations
```bash
# Watch Redis commands in real-time
redis-cli MONITOR
```

Then make API requests and watch the cache operations.

## Complete Test Scenario

### Scenario: Update Country Name

**Step 1: Create Cache**
```bash
curl -X GET "http://localhost:8000/api/v1/area/countries/1" \
  -H "Accept: application/json"
```

Response includes: `"name": "Egypt"`

**Step 2: Verify Cache Exists**
```bash
redis-cli KEYS "countryapi:*"
# Output: 1) "countryapi:find:id=1"
```

**Step 3: Update Country in Admin**
- Change name from "Egypt" to "Egypt Updated"
- Save

**Step 4: Check Logs**
```
[2026-01-29 11:20:00] local.INFO: CountryObserver: Clearing country cache
[2026-01-29 11:20:00] local.INFO: CountryObserver: Cache cleared {"countryapi_keys":1}
```
✅ Cache was cleared!

**Step 5: Verify Cache Deleted**
```bash
redis-cli KEYS "countryapi:*"
# Output: (empty array)
```

**Step 6: Request Again**
```bash
curl -X GET "http://localhost:8000/api/v1/area/countries/1" \
  -H "Accept: application/json"
```

Response includes: `"name": "Egypt Updated"` ✅

## Testing with Postman/Apidog

### Collection Setup
1. Create request: `GET {{base_url}}/api/v1/area/countries`
2. Add query params: `per_page=15`
3. Send request (creates cache)
4. Send again (uses cache - should be faster)
5. Update country in admin
6. Send again (rebuilds cache with new data)

### Performance Testing
Compare response times:
- **First request:** ~100-200ms (no cache)
- **Second request:** ~5-10ms (cached)
- **After update:** ~100-200ms (cache rebuilt)

## Common Issues

### Issue 1: Cache Not Being Created
**Symptom:** Always see `countryapi_keys: 0`

**Cause:** Not making API requests, only using admin panel

**Solution:** Make actual API requests to `/api/v1/area/countries`

### Issue 2: Cache Not Being Cleared
**Symptom:** Old data returned after update

**Cause:** Observer not firing or Redis connection issue

**Solution:** 
```bash
# Check Redis connection
redis-cli PING
# Should return: PONG

# Check observer is registered
php artisan event:list | grep Country
```

### Issue 3: Multiple Cache Clears
**Symptom:** Observer fires multiple times per update

**Solution:** Already fixed with static flag in observer

## Performance Metrics

### Expected Performance
| Scenario | Response Time | Database Queries |
|----------|--------------|------------------|
| First API request | 100-200ms | 1-2 queries |
| Cached API request | 5-10ms | 0 queries |
| After cache clear | 100-200ms | 1-2 queries |

### Cache Hit Rate
Monitor over time:
```bash
# Get cache statistics
redis-cli INFO stats | grep keyspace
```

Target: >80% cache hit rate for country endpoints

## Debugging Commands

### Clear All Cache Manually
```bash
php artisan cache:clear
```

### Clear Only Country Cache
```bash
redis-cli DEL $(redis-cli KEYS "countryapi:*")
```

### Watch Logs in Real-Time
```bash
tail -f storage/logs/laravel.log | grep CountryObserver
```

### Test Cache Service Directly
```php
// In tinker: php artisan tinker
$cache = app(\App\Services\CacheService::class);

// Create test cache
$cache->put('countryapi:test', ['data' => 'test'], 60);

// Check if exists
$cache->has('countryapi:test'); // true

// Clear by pattern
$cleared = $cache->forgetByPattern('countryapi:*');
echo "Cleared: $cleared keys";
```

## Success Criteria

✅ API requests create cache  
✅ Subsequent requests use cache (faster)  
✅ Updating country clears cache  
✅ Next API request rebuilds cache with new data  
✅ Cache keys show in Redis  
✅ Observer logs show correct count when clearing  

---

**Remember:** The cache is for **API performance**, not admin panel performance. Admin panel uses different queries and doesn't need caching since it's used less frequently.
