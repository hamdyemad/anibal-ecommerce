<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Database Monitor Helper
 * 
 * Provides utilities for monitoring database read/write splitting performance
 */
class DatabaseMonitor
{
    /**
     * Track query distribution between read and write connections
     * 
     * @param callable $callback The code to monitor
     * @return array Statistics about query distribution
     */
    public static function trackQueryDistribution(callable $callback): array
    {
        DB::enableQueryLog();
        
        $callback();
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        $stats = [
            'total_queries' => count($queries),
            'read_queries' => 0,
            'write_queries' => 0,
            'slow_queries' => 0,
            'total_time' => 0,
            'queries' => [],
        ];
        
        foreach ($queries as $query) {
            $stats['total_time'] += $query['time'];
            
            // Detect query type
            $sql = strtoupper(trim($query['query']));
            $isWrite = false;
            
            if (preg_match('/^(INSERT|UPDATE|DELETE|CREATE|ALTER|DROP|TRUNCATE)/', $sql)) {
                $isWrite = true;
                $stats['write_queries']++;
            } else {
                $stats['read_queries']++;
            }
            
            // Track slow queries (> 1 second)
            if ($query['time'] > 1000) {
                $stats['slow_queries']++;
            }
            
            $stats['queries'][] = [
                'sql' => $query['query'],
                'time' => $query['time'],
                'type' => $isWrite ? 'write' : 'read',
                'slow' => $query['time'] > 1000,
            ];
        }
        
        return $stats;
    }
    
    /**
     * Log slow queries for monitoring
     * 
     * @param int $threshold Time in milliseconds (default: 1000ms = 1 second)
     * @return void
     */
    public static function logSlowQueries(int $threshold = 1000): void
    {
        DB::listen(function ($query) use ($threshold) {
            if ($query->time > $threshold) {
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName,
                    'threshold' => $threshold . 'ms',
                ]);
            }
        });
    }
    
    /**
     * Check if read/write splitting is active
     * 
     * @return bool
     */
    public static function isSplittingActive(): bool
    {
        $readHosts = config('database.connections.mysql.read.host', []);
        $writeHosts = config('database.connections.mysql.write.host', []);
        $defaultHost = env('DB_HOST', '127.0.0.1');
        
        // Check if any read host is different from default
        foreach ($readHosts as $host) {
            if ($host !== $defaultHost) {
                return true;
            }
        }
        
        // Check if write host is different from default
        foreach ($writeHosts as $host) {
            if ($host !== $defaultHost) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get current database configuration summary
     * 
     * @return array
     */
    public static function getConfigSummary(): array
    {
        return [
            'splitting_active' => self::isSplittingActive(),
            'read_hosts' => config('database.connections.mysql.read.host', []),
            'write_hosts' => config('database.connections.mysql.write.host', []),
            'sticky_sessions' => config('database.connections.mysql.sticky', false),
            'default_connection' => config('database.default'),
        ];
    }
    
    /**
     * Test database connections
     * 
     * @return array Test results
     */
    public static function testConnections(): array
    {
        $results = [
            'default_connection' => [
                'status' => 'unknown',
                'message' => '',
            ],
            'write_connection' => [
                'status' => 'unknown',
                'message' => '',
            ],
            'read_connection' => [
                'status' => 'unknown',
                'message' => '',
            ],
        ];
        
        // Test default connection
        try {
            DB::connection()->getPdo();
            $results['default_connection']['status'] = 'success';
            $results['default_connection']['message'] = 'Connected successfully';
        } catch (\Exception $e) {
            $results['default_connection']['status'] = 'failed';
            $results['default_connection']['message'] = $e->getMessage();
        }
        
        // Test write connection
        try {
            DB::connection()->getPdo();
            $results['write_connection']['status'] = 'success';
            $results['write_connection']['message'] = 'Write connection available';
        } catch (\Exception $e) {
            $results['write_connection']['status'] = 'failed';
            $results['write_connection']['message'] = $e->getMessage();
        }
        
        // Test read connection
        try {
            DB::connection()->getReadPdo();
            $results['read_connection']['status'] = 'success';
            $results['read_connection']['message'] = 'Read connection available';
        } catch (\Exception $e) {
            $results['read_connection']['status'] = 'failed';
            $results['read_connection']['message'] = $e->getMessage();
        }
        
        return $results;
    }
    
    /**
     * Generate a performance report for a given time period
     * 
     * @param callable $callback The code to profile
     * @return array Performance report
     */
    public static function generatePerformanceReport(callable $callback): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $queryStats = self::trackQueryDistribution($callback);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        return [
            'execution_time' => round(($endTime - $startTime) * 1000, 2) . 'ms',
            'memory_used' => round(($endMemory - $startMemory) / 1024 / 1024, 2) . 'MB',
            'query_stats' => $queryStats,
            'read_write_ratio' => $queryStats['read_queries'] > 0 
                ? round($queryStats['read_queries'] / $queryStats['total_queries'] * 100, 2) . '% reads'
                : '0% reads',
            'average_query_time' => $queryStats['total_queries'] > 0
                ? round($queryStats['total_time'] / $queryStats['total_queries'], 2) . 'ms'
                : '0ms',
        ];
    }
}
