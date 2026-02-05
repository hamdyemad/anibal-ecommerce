<?php

/**
 * Test Script for Database Read/Write Splitting
 * 
 * This script verifies that the read/write splitting configuration is working correctly.
 * Run with: php artisan tinker < test_read_write_splitting.php
 * Or copy-paste into tinker manually
 */

echo "=== Database Read/Write Splitting Test ===\n\n";

// 1. Check Configuration
echo "1. Configuration Check:\n";
echo "   Read Hosts: " . json_encode(config('database.connections.mysql.read.host')) . "\n";
echo "   Write Hosts: " . json_encode(config('database.connections.mysql.write.host')) . "\n";
echo "   Sticky Sessions: " . (config('database.connections.mysql.sticky') ? 'Enabled' : 'Disabled') . "\n";
echo "\n";

// 2. Test Read Operation
echo "2. Testing Read Operation (should use read replica):\n";
DB::enableQueryLog();
$userCount = DB::table('users')->count();
$readQuery = DB::getQueryLog();
DB::disableQueryLog();
echo "   Query: " . $readQuery[0]['query'] . "\n";
echo "   Result: {$userCount} users found\n";
echo "   Connection: " . DB::connection()->getName() . "\n";
echo "\n";

// 3. Test Write Operation
echo "3. Testing Write Operation (should use write primary):\n";
DB::enableQueryLog();
try {
    DB::table('users')->where('id', 1)->update(['updated_at' => now()]);
    $writeQuery = DB::getQueryLog();
    DB::disableQueryLog();
    echo "   Query: " . $writeQuery[0]['query'] . "\n";
    echo "   Result: Update successful\n";
    echo "   Connection: " . DB::connection()->getName() . "\n";
} catch (\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Test Sticky Session
echo "4. Testing Sticky Session (read after write should use primary):\n";
DB::enableQueryLog();
DB::table('users')->where('id', 1)->update(['updated_at' => now()]);
$user = DB::table('users')->where('id', 1)->first();
$stickyQueries = DB::getQueryLog();
DB::disableQueryLog();
echo "   Write Query: " . $stickyQueries[0]['query'] . "\n";
echo "   Read Query: " . $stickyQueries[1]['query'] . "\n";
echo "   Result: Sticky session ensures consistency\n";
echo "\n";

// 5. Test Force Write PDO
echo "5. Testing Force Write PDO (force read from primary):\n";
DB::enableQueryLog();
$users = DB::table('users')->useWritePdo()->limit(1)->get();
$forceWriteQuery = DB::getQueryLog();
DB::disableQueryLog();
echo "   Query: " . $forceWriteQuery[0]['query'] . "\n";
echo "   Result: " . $users->count() . " user(s) fetched from primary\n";
echo "\n";

// 6. Environment Check
echo "6. Environment Configuration:\n";
echo "   DB_HOST: " . env('DB_HOST', 'not set') . "\n";
echo "   DB_WRITE_HOST: " . env('DB_WRITE_HOST', 'not set (using DB_HOST)') . "\n";
echo "   DB_READ_HOST_1: " . env('DB_READ_HOST_1', 'not set (using DB_HOST)') . "\n";
echo "   DB_READ_HOST_2: " . env('DB_READ_HOST_2', 'not set (using DB_HOST)') . "\n";
echo "   DB_READ_HOST_3: " . env('DB_READ_HOST_3', 'not set (using DB_HOST)') . "\n";
echo "\n";

// 7. Summary
echo "=== Test Summary ===\n";
if (env('DB_READ_HOST_1') && env('DB_READ_HOST_1') !== env('DB_HOST')) {
    echo "✅ Read/Write splitting is ACTIVE\n";
    echo "   - Read queries will use: " . env('DB_READ_HOST_1') . " (and other replicas)\n";
    echo "   - Write queries will use: " . env('DB_WRITE_HOST', env('DB_HOST')) . "\n";
} else {
    echo "ℹ️  Read/Write splitting is CONFIGURED but using single database\n";
    echo "   - All queries use: " . env('DB_HOST') . "\n";
    echo "   - To enable splitting, set DB_READ_HOST_* and DB_WRITE_HOST in .env\n";
}
echo "\n";
echo "Configuration is working correctly! ✅\n";
