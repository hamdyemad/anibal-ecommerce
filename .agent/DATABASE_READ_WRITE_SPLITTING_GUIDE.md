# Database Read/Write Splitting Implementation Guide

## Status: NOT IMPLEMENTED ⚠️

**Priority:** Medium 🟡  
**Complexity:** Medium  
**Impact:** High performance improvement under load

---

## Problem Statement

### Current Architecture:
```
┌─────────────┐
│   Laravel   │
│ Application │
└──────┬──────┘
       │
       │ ALL queries (READ + WRITE)
       ▼
┌─────────────┐
│   MySQL     │
│  Database   │
└─────────────┘
```

### Issues:
1. **Single Point of Bottleneck:** All queries compete for same database resources
2. **Resource Contention:** Heavy read queries slow down critical writes
3. **Limited Scalability:** Can't scale reads independently from writes
4. **Performance Degradation:** Response times increase under load

### Real-World Impact:
- Product listing queries slow down order creation
- Report generation blocks customer checkouts
- Analytics queries compete with inventory updates
- Dashboard loads affect transaction processing

---

## Recommended Architecture

### With Read/Write Splitting:
```
┌─────────────┐
│   Laravel   │
│ Application │
└──────┬──────┘
       │
       ├─── WRITE queries (INSERT, UPDATE, DELETE)
       │    ▼
       │    ┌─────────────┐
       │    │   MySQL     │
       │    │   MASTER    │
       │    └──────┬──────┘
       │           │
       │           │ Replication
       │           ▼
       └─── READ queries (SELECT)
            ▼
            ┌─────────────┐     ┌─────────────┐
            │   MySQL     │     │   MySQL     │
            │  REPLICA 1  │     │  REPLICA 2  │
            └─────────────┘     └─────────────┘
```

### Benefits:
- ✅ **Load Distribution:** Reads and writes use separate servers
- ✅ **Horizontal Scaling:** Add more read replicas as needed
- ✅ **Better Performance:** Queries don't compete for resources
- ✅ **High Availability:** Replicas can serve as failover
- ✅ **Improved Response Times:** Faster queries under load

---

## Implementation Steps

### Step 1: Set Up Database Replication

**Prerequisites:**
- MySQL Master database (your current database)
- One or more MySQL Replica servers
- Network connectivity between master and replicas

**MySQL Master Configuration:**
```ini
# /etc/mysql/my.cnf or /etc/my.cnf
[mysqld]
server-id = 1
log_bin = /var/log/mysql/mysql-bin.log
binlog_do_db = your_database_name
bind-address = 0.0.0.0
```

**MySQL Replica Configuration:**
```ini
# /etc/mysql/my.cnf or /etc/my.cnf
[mysqld]
server-id = 2
relay-log = /var/log/mysql/mysql-relay-bin.log
log_bin = /var/log/mysql/mysql-bin.log
binlog_do_db = your_database_name
read_only = 1
```

**Set Up Replication:**
```sql
-- On Master: Create replication user
CREATE USER 'replication_user'@'%' IDENTIFIED BY 'strong_password';
GRANT REPLICATION SLAVE ON *.* TO 'replication_user'@'%';
FLUSH PRIVILEGES;
SHOW MASTER STATUS;  -- Note the File and Position

-- On Replica: Configure replication
CHANGE MASTER TO
  MASTER_HOST='master_ip_address',
  MASTER_USER='replication_user',
  MASTER_PASSWORD='strong_password',
  MASTER_LOG_FILE='mysql-bin.000001',  -- From SHOW MASTER STATUS
  MASTER_LOG_POS=12345;                -- From SHOW MASTER STATUS

START SLAVE;
SHOW SLAVE STATUS\G  -- Verify replication is working
```

---

### Step 2: Update Laravel Configuration

**File:** `config/database.php`

Replace the `mysql` connection with read/write splitting configuration:

```php
'mysql' => [
    'driver' => 'mysql',
    'read' => [
        'host' => [
            env('DB_READ_HOST_1', '127.0.0.1'),
            env('DB_READ_HOST_2', '127.0.0.1'),  // Optional: multiple replicas
        ],
    ],
    'write' => [
        'host' => [
            env('DB_WRITE_HOST', '127.0.0.1'),
        ],
    ],
    'sticky' => true,  // Important: ensures reads after writes use master
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
],
```

---

### Step 3: Update Environment Variables

**File:** `.env`

Add the following configuration:

```env
# Master Database (WRITE operations)
DB_WRITE_HOST=master-db-host.example.com

# Read Replicas (READ operations)
DB_READ_HOST_1=replica1-db-host.example.com
DB_READ_HOST_2=replica2-db-host.example.com

# Existing variables remain the same
DB_CONNECTION=mysql
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**For Production (.env.production):**
```env
DB_WRITE_HOST=mysql-95884d13-oeccc512e.database.cloud.ovh.net
DB_READ_HOST_1=mysql-replica1.database.cloud.ovh.net
DB_READ_HOST_2=mysql-replica2.database.cloud.ovh.net
DB_PORT=20184
DB_DATABASE=bnaia-multivendor
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

---

### Step 4: Understanding Sticky Connections

**What is `'sticky' => true`?**

When enabled, Laravel ensures that any reads performed during the same request after a write will use the master database, not replicas.

**Why is this important?**

```php
// Without sticky:
DB::table('orders')->insert([...]);  // Write to MASTER
$order = DB::table('orders')->find($id);  // Read from REPLICA (might not have data yet!)

// With sticky:
DB::table('orders')->insert([...]);  // Write to MASTER
$order = DB::table('orders')->find($id);  // Read from MASTER (guaranteed to have data)
```

**Replication Lag:**
- Data written to master takes time to replicate (usually milliseconds)
- Without sticky, you might read stale data
- Sticky connections prevent this issue

---

### Step 5: Force Specific Connection (Advanced)

**Force Write Connection (Master):**
```php
// When you need guaranteed fresh data
$order = DB::connection('mysql')->table('orders')->find($id);

// Or use onWriteConnection()
$order = DB::table('orders')->onWriteConnection()->find($id);
```

**Force Read Connection (Replica):**
```php
// For heavy analytics that can tolerate slight lag
$stats = DB::table('orders')
    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
    ->groupBy('date')
    ->get();
```

---

## Testing the Configuration

### Test 1: Verify Connections

Create a test command:

```php
// app/Console/Commands/TestDatabaseConnections.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDatabaseConnections extends Command
{
    protected $signature = 'db:test-connections';
    protected $description = 'Test read/write database connections';

    public function handle()
    {
        $this->info('Testing database connections...');
        
        // Test write connection
        try {
            $writeHost = DB::connection('mysql')
                ->selectOne('SELECT @@hostname as host, "WRITE" as type');
            $this->info("✓ Write Connection: {$writeHost->host}");
        } catch (\Exception $e) {
            $this->error("✗ Write Connection Failed: {$e->getMessage()}");
        }
        
        // Test read connection
        try {
            $readHost = DB::connection('mysql')
                ->table(DB::raw('(SELECT 1) as t'))
                ->selectRaw('@@hostname as host, "READ" as type')
                ->first();
            $this->info("✓ Read Connection: {$readHost->host}");
        } catch (\Exception $e) {
            $this->error("✗ Read Connection Failed: {$e->getMessage()}");
        }
        
        // Test sticky connection
        $this->info("\nTesting sticky connections...");
        DB::table('users')->where('id', 1)->update(['updated_at' => now()]);
        $user = DB::table('users')->find(1);
        $this->info("✓ Sticky connection working (read after write)");
        
        return 0;
    }
}
```

Run the test:
```bash
php artisan db:test-connections
```

---

### Test 2: Monitor Query Distribution

Add logging to see which connection is used:

```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    if (config('app.debug')) {
        DB::listen(function ($query) {
            $connection = $query->connectionName;
            $type = str_starts_with(strtoupper($query->sql), 'SELECT') ? 'READ' : 'WRITE';
            
            \Log::debug("DB Query [{$connection}] [{$type}]: {$query->sql}");
        });
    }
}
```

---

## Monitoring Replication Health

### Check Replication Status

**On Replica Server:**
```sql
SHOW SLAVE STATUS\G

-- Key metrics to monitor:
-- Slave_IO_Running: Should be "Yes"
-- Slave_SQL_Running: Should be "Yes"
-- Seconds_Behind_Master: Should be low (< 1 second ideally)
-- Last_Error: Should be empty
```

### Create Monitoring Command

```php
// app/Console/Commands/CheckReplicationHealth.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckReplicationHealth extends Command
{
    protected $signature = 'db:check-replication';
    protected $description = 'Check database replication health';

    public function handle()
    {
        try {
            // This query only works on replica servers
            $status = DB::connection('mysql')
                ->select('SHOW SLAVE STATUS')[0] ?? null;
            
            if (!$status) {
                $this->warn('Not a replica server or replication not configured');
                return 1;
            }
            
            $ioRunning = $status->Slave_IO_Running ?? 'No';
            $sqlRunning = $status->Slave_SQL_Running ?? 'No';
            $secondsBehind = $status->Seconds_Behind_Master ?? 'Unknown';
            $lastError = $status->Last_Error ?? '';
            
            $this->info("Replication Status:");
            $this->info("  IO Thread: {$ioRunning}");
            $this->info("  SQL Thread: {$sqlRunning}");
            $this->info("  Seconds Behind Master: {$secondsBehind}");
            
            if ($lastError) {
                $this->error("  Last Error: {$lastError}");
            }
            
            if ($ioRunning === 'Yes' && $sqlRunning === 'Yes' && $secondsBehind < 5) {
                $this->info("\n✓ Replication is healthy");
                return 0;
            } else {
                $this->error("\n✗ Replication has issues");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("Error checking replication: {$e->getMessage()}");
            return 1;
        }
    }
}
```

---

## Best Practices

### 1. Use Sticky Connections (Already Configured)
```php
'sticky' => true,  // Ensures read-after-write consistency
```

### 2. Identify Heavy Read Queries
Queries that should use replicas:
- ✅ Product listings and filters
- ✅ Dashboard analytics
- ✅ Reports and statistics
- ✅ Search operations
- ✅ Public API endpoints

### 3. Identify Critical Write Queries
Queries that need immediate consistency:
- ✅ Order creation
- ✅ Payment processing
- ✅ Inventory updates
- ✅ User authentication

### 4. Handle Replication Lag
```php
// For critical reads after writes, force master:
DB::table('orders')->onWriteConnection()->find($orderId);

// For analytics that can tolerate lag, use default (replica):
DB::table('orders')->selectRaw('COUNT(*) as total')->first();
```

### 5. Monitor Performance
- Track query distribution (reads vs writes)
- Monitor replication lag
- Set up alerts for replication failures
- Log slow queries on both master and replicas

---

## Rollback Plan

If issues occur, you can quickly rollback:

**1. Update `.env`:**
```env
# Point both read and write to master
DB_WRITE_HOST=master-db-host.example.com
DB_READ_HOST_1=master-db-host.example.com
```

**2. Or revert `config/database.php`:**
```php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),  // Single host
    // Remove 'read' and 'write' arrays
    // ...
],
```

**3. Clear config cache:**
```bash
php artisan config:clear
php artisan config:cache
```

---

## Cost Considerations

### Infrastructure Costs:
- **1 Master + 1 Replica:** ~2x database hosting cost
- **1 Master + 2 Replicas:** ~3x database hosting cost

### ROI Analysis:
- **Small Traffic (<1000 req/min):** May not need replicas yet
- **Medium Traffic (1000-10000 req/min):** 1 replica recommended
- **High Traffic (>10000 req/min):** 2+ replicas recommended

### When to Implement:
- ✅ Database CPU consistently >70%
- ✅ Slow query times during peak hours
- ✅ Read queries outnumber writes 3:1 or more
- ✅ Planning for growth/scaling

---

## Alternative Solutions

If full read/write splitting is too complex, consider:

### 1. Query Optimization
- Add proper indexes
- Optimize slow queries
- Use eager loading

### 2. Caching Layer
- Redis for frequently accessed data
- Cache product listings
- Cache dashboard statistics

### 3. Database Connection Pooling
- Use PgBouncer or ProxySQL
- Reduces connection overhead

### 4. Vertical Scaling
- Upgrade database server resources
- More CPU, RAM, faster disks

---

## Summary

### Current Status: ⚠️ NOT IMPLEMENTED

**To Implement:**
1. Set up MySQL replication (Master → Replica)
2. Update `config/database.php` with read/write configuration
3. Update `.env` with master and replica hosts
4. Test connections and replication
5. Monitor performance and replication health

**Priority:** Medium 🟡
- Not critical for current traffic
- Important for scaling
- Implement before traffic increases significantly

**Estimated Effort:**
- Infrastructure setup: 2-4 hours
- Laravel configuration: 30 minutes
- Testing and monitoring: 1-2 hours
- **Total: 4-7 hours**

---

**Next Steps:**
1. Consult with infrastructure team about replica setup
2. Test in staging environment first
3. Monitor replication lag and performance
4. Gradually roll out to production
5. Set up monitoring and alerts

---

**Documentation Date:** January 29, 2026  
**Status:** Pending Implementation
