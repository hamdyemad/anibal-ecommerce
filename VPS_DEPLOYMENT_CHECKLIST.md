# VPS Deployment Checklist

## Before Upload
- ✅ All PSR-4 autoloading warnings fixed
- ✅ Cache directories created
- ✅ Storage directories structure complete

## After Uploading to VPS

### 1. Set Correct Permissions
```bash
# Set storage and bootstrap/cache permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership (replace www-data with your web server user)
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Environment Configuration
```bash
# Copy and configure your .env file
cp .env.example .env
nano .env  # Edit with your production settings

# Generate application key if needed
php artisan key:generate
```

### 4. Clear and Optimize Cache
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 5. Create Storage Link
```bash
php artisan storage:link
```

### 6. Run Migrations (if needed)
```bash
php artisan migrate --force
```

### 7. Set Up Queue Worker (if using queues)
```bash
# For supervisor or systemd
php artisan queue:work --daemon
```

## Important Production Settings in .env

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use Redis for better performance if available
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Or use file/database if Redis not available
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

## Troubleshooting Cache Issues

If you encounter cache errors:

```bash
# Full cache reset
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload

# Verify permissions
ls -la storage/framework/cache
ls -la storage/framework/sessions
ls -la storage/framework/views
```

## Performance Optimization

```bash
# After deployment, run these for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# For modules
php artisan module:optimize
```

## Notes
- Ensure your web server (Apache/Nginx) points to the `public` directory
- Enable OPcache in PHP for better performance
- Consider using Redis for cache and sessions in production
- Set up proper backup for your database and storage files
