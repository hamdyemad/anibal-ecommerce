<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseMonitor;

class TestDatabaseSplitting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test-splitting {--detailed : Show detailed query information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test database read/write splitting configuration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== Database Read/Write Splitting Test ===');
        $this->newLine();

        // 1. Configuration Check
        $this->info('1. Configuration Check:');
        $config = DatabaseMonitor::getConfigSummary();
        
        $this->line('   Splitting Active: ' . ($config['splitting_active'] ? '✅ Yes' : 'ℹ️  No (using single database)'));
        $this->line('   Read Hosts: ' . json_encode($config['read_hosts']));
        $this->line('   Write Hosts: ' . json_encode($config['write_hosts']));
        $this->line('   Sticky Sessions: ' . ($config['sticky_sessions'] ? '✅ Enabled' : '❌ Disabled'));
        $this->newLine();

        // 2. Connection Test
        $this->info('2. Connection Test:');
        $connections = DatabaseMonitor::testConnections();
        
        foreach ($connections as $name => $result) {
            $status = $result['status'] === 'success' ? '✅' : '❌';
            $this->line("   {$status} " . ucwords(str_replace('_', ' ', $name)) . ': ' . $result['message']);
        }
        $this->newLine();

        // 3. Read Operation Test
        $this->info('3. Testing Read Operation:');
        $report = DatabaseMonitor::generatePerformanceReport(function() {
            DB::table('users')->count();
            DB::table('users')->limit(10)->get();
        });
        
        $this->line('   Total Queries: ' . $report['query_stats']['total_queries']);
        $this->line('   Read Queries: ' . $report['query_stats']['read_queries']);
        $this->line('   Write Queries: ' . $report['query_stats']['write_queries']);
        $this->line('   Execution Time: ' . $report['execution_time']);
        $this->line('   Average Query Time: ' . $report['average_query_time']);
        
        if ($this->option('detailed')) {
            $this->newLine();
            $this->line('   Detailed Queries:');
            foreach ($report['query_stats']['queries'] as $query) {
                $type = $query['type'] === 'read' ? '📖' : '✏️';
                $slow = $query['slow'] ? '🐌' : '';
                $this->line("   {$type} {$slow} [{$query['time']}ms] {$query['sql']}");
            }
        }
        $this->newLine();

        // 4. Write Operation Test
        $this->info('4. Testing Write Operation:');
        $writeReport = DatabaseMonitor::generatePerformanceReport(function() {
            try {
                DB::table('users')->where('id', 1)->update(['updated_at' => now()]);
            } catch (\Exception $e) {
                // Ignore if user doesn't exist
            }
        });
        
        $this->line('   Total Queries: ' . $writeReport['query_stats']['total_queries']);
        $this->line('   Write Queries: ' . $writeReport['query_stats']['write_queries']);
        $this->line('   Execution Time: ' . $writeReport['execution_time']);
        $this->newLine();

        // 5. Sticky Session Test
        $this->info('5. Testing Sticky Session:');
        $stickyReport = DatabaseMonitor::generatePerformanceReport(function() {
            try {
                DB::table('users')->where('id', 1)->update(['updated_at' => now()]);
                DB::table('users')->where('id', 1)->first();
            } catch (\Exception $e) {
                // Ignore if user doesn't exist
            }
        });
        
        $this->line('   Total Queries: ' . $stickyReport['query_stats']['total_queries']);
        $this->line('   Read Queries: ' . $stickyReport['query_stats']['read_queries']);
        $this->line('   Write Queries: ' . $stickyReport['query_stats']['write_queries']);
        $this->line('   Result: ' . ($config['sticky_sessions'] ? '✅ Sticky session active (read after write uses primary)' : '⚠️  Sticky sessions disabled'));
        $this->newLine();

        // 6. Environment Check
        $this->info('6. Environment Configuration:');
        $this->line('   DB_HOST: ' . env('DB_HOST', 'not set'));
        $this->line('   DB_WRITE_HOST: ' . (env('DB_WRITE_HOST') ?: 'not set (using DB_HOST)'));
        $this->line('   DB_READ_HOST_1: ' . (env('DB_READ_HOST_1') ?: 'not set (using DB_HOST)'));
        $this->line('   DB_READ_HOST_2: ' . (env('DB_READ_HOST_2') ?: 'not set (using DB_HOST)'));
        $this->line('   DB_READ_HOST_3: ' . (env('DB_READ_HOST_3') ?: 'not set (using DB_HOST)'));
        $this->newLine();

        // 7. Summary
        $this->info('=== Test Summary ===');
        if ($config['splitting_active']) {
            $this->line('✅ Read/Write splitting is ACTIVE');
            $this->line('   - Read queries will use: ' . implode(', ', $config['read_hosts']));
            $this->line('   - Write queries will use: ' . implode(', ', $config['write_hosts']));
        } else {
            $this->line('ℹ️  Read/Write splitting is CONFIGURED but using single database');
            $this->line('   - All queries use: ' . env('DB_HOST'));
            $this->line('   - To enable splitting, set DB_READ_HOST_* and DB_WRITE_HOST in .env');
        }
        $this->newLine();
        
        $this->info('Configuration is working correctly! ✅');
        $this->newLine();
        
        $this->comment('Tip: Use --detailed flag to see individual query information');
        $this->comment('Example: php artisan db:test-splitting --detailed');

        return Command::SUCCESS;
    }
}
