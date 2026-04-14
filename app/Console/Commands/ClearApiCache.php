<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ClearApiCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-api {--type=all : Type of cache to clear (all, categories, departments, subcategories, brands)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear API cache for categories, departments, subcategories, and brands';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        
        $patterns = $this->getPatterns($type);
        
        if (empty($patterns)) {
            $this->error("Invalid type: {$type}");
            $this->info("Valid types: all, categories, departments, subcategories, brands");
            return 1;
        }

        $this->info("🔍 Clearing {$type} cache...");
        
        $cleared = 0;
        $cacheDriver = config('cache.default');

        if ($cacheDriver === 'database') {
            $cacheTable = config('cache.stores.database.table', 'cache');
            
            foreach ($patterns as $pattern) {
                $deleted = DB::table($cacheTable)
                    ->where('key', 'like', $pattern . '%')
                    ->delete();
                
                $cleared += $deleted;
                $this->line("   ✓ Cleared {$deleted} entries for: {$pattern}*");
            }
        } else {
            // For file cache driver
            foreach ($patterns as $pattern) {
                // Use Cache::flush() for simplicity with file driver
                // Or implement specific pattern matching if needed
                Cache::flush();
                $this->line("   ✓ Flushed all cache (file driver doesn't support pattern matching)");
                break;
            }
        }

        $this->newLine();
        $this->info("✅ Total cleared: {$cleared} cache entries");
        $this->info("📋 Cache driver: {$cacheDriver}");
        
        return 0;
    }

    /**
     * Get cache patterns based on type
     */
    protected function getPatterns(string $type): array
    {
        return match($type) {
            'all' => [
                'api_categories_',
                'departments_',
                'subcategories_',
                'api_brands_',
            ],
            'categories' => ['api_categories_'],
            'departments' => ['departments_'],
            'subcategories' => ['subcategories_'],
            'brands' => ['api_brands_'],
            default => [],
        };
    }
}
