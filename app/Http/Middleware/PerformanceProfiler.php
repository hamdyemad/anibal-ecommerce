<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PerformanceProfiler
{
    /**
     * Handle an incoming request and profile its performance
     */
    public function handle(Request $request, Closure $next)
    {
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
        
        // Log performance data
        $performanceData = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'execution_time_ms' => round($executionTime, 2),
            'memory_used_mb' => round($memoryUsed, 2),
            'query_count' => count($queries),
            'total_query_time_ms' => round($totalQueryTime, 2),
            'queries' => array_map(function($query) {
                return [
                    'sql' => $query['query'],
                    'time_ms' => $query['time'],
                    'bindings' => $query['bindings']
                ];
            }, $queries)
        ];
        
        Log::channel('performance')->info('Request Performance', $performanceData);
        
        // Also add to response headers for easy debugging
        if (config('app.debug')) {
            $response->headers->set('X-Debug-Time', round($executionTime, 2) . 'ms');
            $response->headers->set('X-Debug-Queries', count($queries));
            $response->headers->set('X-Debug-Query-Time', round($totalQueryTime, 2) . 'ms');
            $response->headers->set('X-Debug-Memory', round($memoryUsed, 2) . 'MB');
        }
        
        return $response;
    }
}
