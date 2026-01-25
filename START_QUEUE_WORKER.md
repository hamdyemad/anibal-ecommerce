# How to Start Queue Worker for Product Import

## The Problem
The progress bar stays at 0% because the queue worker is not running. Without a queue worker, the import job sits in the queue and never processes.

## Solution: Start the Queue Worker

### Option 1: For Development (Recommended)
Open a **NEW terminal/command prompt** and run:

```bash
php artisan queue:work
```

Keep this terminal window open while importing products.

### Option 2: For Development with Auto-Reload
If you're making code changes and want the worker to reload automatically:

```bash
php artisan queue:listen
```

### Option 3: Process One Job at a Time (Testing)
To process just one job:

```bash
php artisan queue:work --once
```

## How to Verify It's Working

1. Start the queue worker in a separate terminal
2. Go to the bulk upload page
3. Upload an Excel file
4. You should see in the queue worker terminal:
   ```
   [2026-01-25 12:00:00][batch-id] Processing: Modules\CatalogManagement\app\Jobs\ProcessProductImport
   [2026-01-25 12:00:05][batch-id] Processed:  Modules\CatalogManagement\app\Jobs\ProcessProductImport
   ```
5. The progress bar should now increase from 0% to 100%

## Troubleshooting

### Progress Still at 0%?
1. Check if queue worker is actually running (look at the terminal)
2. Check browser console (F12) for JavaScript errors
3. Check Laravel logs: `storage/logs/laravel.log`

### Queue Worker Stops?
Restart it:
```bash
php artisan queue:work
```

### Want to See Failed Jobs?
```bash
php artisan queue:failed
```

### Want to Retry Failed Jobs?
```bash
php artisan queue:retry all
```

## For Production

For production environments, use Supervisor to keep the queue worker running:

1. Install Supervisor
2. Create config file: `/etc/supervisor/conf.d/laravel-worker.conf`
3. Add configuration:
   ```ini
   [program:laravel-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /path/to/your/project/artisan queue:work --sleep=3 --tries=3
   autostart=true
   autorestart=true
   user=www-data
   numprocs=1
   redirect_stderr=true
   stdout_logfile=/path/to/your/project/storage/logs/worker.log
   ```
4. Start Supervisor:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start laravel-worker:*
   ```

## Quick Test

To test if everything is configured correctly:

1. Open Terminal 1: `php artisan queue:work`
2. Open Terminal 2: Go to bulk upload page and upload a file
3. Watch Terminal 1 - you should see the job processing
4. Watch the browser - progress bar should increase

## Important Notes

- **You MUST keep the queue worker running** for the import to process
- Each time you restart your computer, you need to start the queue worker again
- For development, just run `php artisan queue:work` in a separate terminal
- The progress bar will only update if the queue worker is processing the job
