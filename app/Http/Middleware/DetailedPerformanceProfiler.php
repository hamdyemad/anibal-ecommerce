<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DetailedPerformanceProfiler
{
    private static $checkpoints = [];
    
    /**
     * Handle an incoming request with detailed checkpoint tracking
     */
    public function handle(Request $request, Closure $next)
    {
        self::checkpoint('Request Start');
        
        // Enable query logging
        DB::enableQueryLog();
        
        // Track before middleware
        self::checkpoint('Before Middleware Chain');
        
        // Process request
        $response = $next($request);
        
        // Track after middleware
        self::checkpoint('After Middleware Chain');
        
        // Get queries
        $queries = DB::getQueryLog();
        
        // Calculate metrics
        $totalTime = 0;
        $checkpointData = [];
        
        for ($i = 1; $i < count(self::$checkpoints); $i++) {
            $prev = self::$checkpoints[$i - 1];
            $curr = self::$checkpoints[$i];
            $duration = ($curr['time'] - $prev['time']) * 1000;
            $totalTime += $duration;
            
            $checkpointData[] = [
                'from' => $prev['label'],
                'to' => $curr['label'],
                'duration_ms' => round($duration, 2),
                'memory_mb' => round(($curr['memory'] - $prev['memory']) / 1024 / 1024, 2)
            ];
        }
        
        // Group queries by type
        $queryStats = [
            'select' => 0,
            'insert' => 0,
            'update' => 0,
            'delete' => 0,
            'other' => 0
        ];
        
        foreach ($queries as $query) {
            $sql = strtolower(trim($query['query']));
            if (strpos($sql, 'select') === 0) $queryStats['select']++;
            elseif (strpos($sql, 'insert') === 0) $queryStats['insert']++;
            elseif (strpos($sql, 'update') === 0) $queryStats['update']++;
            elseif (strpos($sql, 'delete') === 0) $queryStats['delete']++;
            else $queryStats['other']++;
        }
        
        // Log detailed performance
        Log::channel('performance')->info('=== DETAILED PERFORMANCE PROFILE ===', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'total_time_ms' => round($totalTime, 2),
            'checkpoints' => $checkpointData,
            'query_stats' => $queryStats,
            'total_queries' => count($queries),
            'slow_queries' => array_filter($queries, function($q) {
                return $q['time'] > 10; // Queries slower than 10ms
            }),
            'all_queries' => $queries
        ]);
        
        // Add debug headers
        if (config('app.debug')) {
            $response->headers->set('X-Total-Time', round($totalTime, 2) . 'ms');
            $response->headers->set('X-Query-Count', count($queries));
            $response->headers->set('X-Checkpoint-Count', count($checkpointData));
        }
        
        // Reset for next request
        self::$checkpoints = [];
        
        return $response;
    }
    
    /**
     * Add a performance checkpoint
     */
    public static function checkpoint(string $label)
    {
        self::$checkpoints[] = [
            'label' => $label,
            'time' => microtime(true),
            'memory' => memory_get_usage()
        ];
    }
}
