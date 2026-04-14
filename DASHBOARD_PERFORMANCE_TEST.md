# Dashboard Performance Testing Guide

## 🎯 Quick Test

1. **Open the Performance Viewer:**
   ```
   http://127.0.0.1:8000/performance-viewer.html
   ```

2. **Make sure you're logged in first:**
   - Open http://127.0.0.1:8000 in another tab
   - Login to your account
   - Keep that tab open

3. **Test the Dashboard:**
   - Click the "📊 Test Dashboard" button
   - Or enter custom URL: `http://127.0.0.1:8000/en/eg/admin/dashboard`
   - Click "🔍 Test Performance"

## 📊 What to Look For

### Good Performance:
- ✅ Browser Load Time: < 500ms
- ✅ Server Time: < 50ms
- ✅ Query Count: < 20 queries
- ✅ Query Time: < 100ms

### Warning Signs:
- ⚠️ Browser Load Time: 500ms - 1000ms
- ⚠️ Server Time: 50ms - 200ms
- ⚠️ Query Count: 20-50 queries
- ⚠️ Query Time: 100ms - 500ms

### Poor Performance:
- ❌ Browser Load Time: > 1000ms
- ❌ Server Time: > 200ms
- ❌ Query Count: > 50 queries
- ❌ Query Time: > 500ms

## 🔍 Performance Headers

The system now adds debug headers to every response (when PERFORMANCE_PROFILING=true):

- `X-Debug-Time` - Total server execution time
- `X-Query-Count` - Number of database queries
- `X-Debug-Query-Time` - Total time spent on queries
- `X-Debug-Memory` - Memory used

You can see these in:
1. Browser DevTools → Network tab → Select request → Headers
2. The performance viewer automatically displays them

## 🚀 Common Dashboard Performance Issues

### Issue 1: N+1 Queries
**Symptom:** High query count (50+)
**Solution:** Add eager loading to relationships

### Issue 2: Slow Queries
**Symptom:** High query time but low query count
**Solution:** Add database indexes, optimize queries

### Issue 3: Heavy Middleware
**Symptom:** High server time but low query time
**Solution:** Optimize middleware, add caching

### Issue 4: Asset Loading
**Symptom:** High browser time but low server time
**Solution:** Optimize assets, use CDN, enable compression

## 💡 Testing Different Pages

You can test any page by entering its URL:

```
Login Page:
http://127.0.0.1:8000

Dashboard:
http://127.0.0.1:8000/en/eg/admin/dashboard

Products:
http://127.0.0.1:8000/en/eg/admin/products

Orders:
http://127.0.0.1:8000/en/eg/admin/orders
```

## 🔧 Enable/Disable Profiling

To disable performance profiling in production:

1. Edit `.env` file
2. Set `PERFORMANCE_PROFILING=false`
3. Run `php artisan config:clear`

## 📝 Notes

- Performance profiling adds minimal overhead (~1-2ms)
- Always test with a warm cache (visit page twice)
- Test with realistic data volumes
- Compare before/after optimization results
