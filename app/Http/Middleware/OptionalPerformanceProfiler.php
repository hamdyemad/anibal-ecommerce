<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptionalPerformanceProfiler
{
    /**
     * Handle an incoming request with optional performance profiling
     * Only runs if PERFORMANCE_PROFILING=true in .env
     */
    public function handle(Request $request, Closure $next)
    {
        // Only profile if enabled in config
        if (!config('app.performance_profiling', false)) {
            return $next($request);
        }

        // Start timing
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Enable query logging
        DB::enableQueryLog();
        
        // Process request
        $response = $next($request);
        
        // Calculate metrics
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $executionTime = ($endTime - $startTime) * 1000; // ms
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // MB
        $queries = DB::getQueryLog();
        
        // Calculate total query time
        $totalQueryTime = array_sum(array_column($queries, 'time'));
        
        // Add to response headers for easy debugging
        $response->headers->set('X-Debug-Time', round($executionTime, 2) . 'ms');
        $response->headers->set('X-Query-Count', count($queries));
        $response->headers->set('X-Debug-Query-Time', round($totalQueryTime, 2) . 'ms');
        $response->headers->set('X-Debug-Memory', round($memoryUsed, 2) . 'MB');
        
        return $response;
    }
}
