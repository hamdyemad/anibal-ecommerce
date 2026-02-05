# Production Deployment Guide - Database Read Replicas

## Quick Setup Checklist

### Prerequisites
- [ ] Primary database server running MySQL 5.7+ or 8.0+
- [ ] At least 1 replica server (recommended: 2-3 replicas)
- [ ] Network connectivity between primary and replicas
- [ ] Same MySQL version on all servers

### Step 1: Configure Primary Database (Master)

#### 1.1 Edit MySQL Configuration
```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Add these lines:
```ini
[mysqld]
server-id=1
log-bin=mysql-bin
binlog-do-db=eramo
binlog-format=ROW
expire_logs_days=7
max_binlog_size=100M
```

#### 1.2 Restart MySQL
```bash
sudo systemctl restart mysql
```

#### 1.3 Create Replication User
```sql
mysql -u root -p

CREATE USER 'replicator'@'%' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT REPLICATION SLAVE ON *.* TO 'replicator'@'%';
FLUSH PRIVILEGES;

-- Get master status (IMPORTANT: Note these values!)
SHOW MASTER STATUS;
```

**Save the output:**
- File: `mysql-bin.000001` (example)
- Position: `12345` (example)

### Step 2: Configure Replica Servers (Slaves)

Repeat for each replica server:

#### 2.1 Edit MySQL Configuration
```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Add these lines (increment server-id for each replica):
```ini
[mysqld]
server-id=2  # Use 3, 4, etc. for additional replicas
relay-log=mysql-relay-bin
read-only=1
log-bin=mysql-bin
binlog-format=ROW
```

#### 2.2 Restart MySQL
```bash
sudo systemctl restart mysql
```

#### 2.3 Copy Database from Primary
```bash
# On primary server, create a backup
mysqldump -u root -p --single-transaction --master-data=2 eramo > eramo_backup.sql

# Transfer to replica server
scp eramo_backup.sql user@replica-server:/tmp/

# On replica server, import the backup
mysql -u root -p eramo < /tmp/eramo_backup.sql
```

#### 2.4 Configure Replication
```sql
mysql -u root -p

CHANGE MASTER TO
  MASTER_HOST='PRIMARY_SERVER_IP',
  MASTER_USER='replicator',
  MASTER_PASSWORD='STRONG_PASSWORD_HERE',
  MASTER_LOG_FILE='mysql-bin.000001',  # From SHOW MASTER STATUS
  MASTER_LOG_POS=12345;  # From SHOW MASTER STATUS

START SLAVE;

-- Verify replication is working
SHOW SLAVE STATUS\G
```

**Check these values:**
- `Slave_IO_Running: Yes`
- `Slave_SQL_Running: Yes`
- `Seconds_Behind_Master: 0` (or very low)

### Step 3: Configure Laravel Application

#### 3.1 Update .env File
```env
# Primary database (writes)
DB_WRITE_HOST=primary-db.example.com
DB_PORT=3306
DB_DATABASE=eramo
DB_USERNAME=root
DB_PASSWORD=your_password

# Read replicas (reads)
DB_READ_HOST_1=replica-1.example.com
DB_READ_HOST_2=replica-2.example.com
DB_READ_HOST_3=replica-3.example.com
```

#### 3.2 Clear Configuration Cache
```bash
php artisan config:clear
php artisan config:cache
```

#### 3.3 Test Configuration
```bash
php artisan db:test-splitting --detailed
```

### Step 4: Verify Replication

#### 4.1 Test Write on Primary
```sql
-- On primary server
mysql -u root -p eramo

INSERT INTO users (name, email, created_at, updated_at) 
VALUES ('Test User', 'test@example.com', NOW(), NOW());
```

#### 4.2 Verify on Replica
```sql
-- On replica server (should see the new user)
mysql -u root -p eramo

SELECT * FROM users WHERE email = 'test@example.com';
```

#### 4.3 Monitor Replication Lag
```sql
-- On replica server
SHOW SLAVE STATUS\G

-- Look for:
-- Seconds_Behind_Master: Should be 0 or very low (< 1 second)
```

### Step 5: Application Testing

#### 5.1 Test Read Operations
```bash
php artisan tinker
```

```php
// Should use read replica
DB::table('users')->count();

// Check query log
DB::enableQueryLog();
DB::table('users')->limit(10)->get();
dd(DB::getQueryLog());
```

#### 5.2 Test Write Operations
```php
// Should use primary
DB::table('users')->where('id', 1)->update(['updated_at' => now()]);

// Verify sticky session (should also use primary)
$user = DB::table('users')->where('id', 1)->first();
```

#### 5.3 Load Test
```bash
# Install Apache Bench if not available
sudo apt-get install apache2-utils

# Test read-heavy endpoint (e.g., dashboard)
ab -n 1000 -c 50 http://your-app.com/admin/dashboard
```

### Step 6: Monitoring Setup

#### 6.1 Enable Slow Query Logging
Add to `app/Providers/AppServiceProvider.php`:

```php
use App\Helpers\DatabaseMonitor;

public function boot()
{
    if (config('app.env') === 'production') {
        DatabaseMonitor::logSlowQueries(1000); // Log queries > 1 second
    }
}
```

#### 6.2 Set Up Replication Monitoring
Create a cron job to check replication status:

```bash
crontab -e
```

Add:
```bash
*/5 * * * * /usr/local/bin/check_replication.sh
```

Create `/usr/local/bin/check_replication.sh`:
```bash
#!/bin/bash

SLAVE_STATUS=$(mysql -u root -p'password' -e "SHOW SLAVE STATUS\G")
IO_RUNNING=$(echo "$SLAVE_STATUS" | grep "Slave_IO_Running:" | awk '{print $2}')
SQL_RUNNING=$(echo "$SLAVE_STATUS" | grep "Slave_SQL_Running:" | awk '{print $2}')
SECONDS_BEHIND=$(echo "$SLAVE_STATUS" | grep "Seconds_Behind_Master:" | awk '{print $2}')

if [ "$IO_RUNNING" != "Yes" ] || [ "$SQL_RUNNING" != "Yes" ]; then
    echo "ALERT: Replication stopped on $(hostname)" | mail -s "Replication Alert" admin@example.com
fi

if [ "$SECONDS_BEHIND" -gt 10 ]; then
    echo "WARNING: Replication lag is ${SECONDS_BEHIND} seconds on $(hostname)" | mail -s "Replication Lag Warning" admin@example.com
fi
```

Make it executable:
```bash
chmod +x /usr/local/bin/check_replication.sh
```

### Step 7: Firewall Configuration

#### 7.1 On Primary Server
```bash
# Allow replicas to connect on MySQL port
sudo ufw allow from REPLICA_1_IP to any port 3306
sudo ufw allow from REPLICA_2_IP to any port 3306
sudo ufw allow from REPLICA_3_IP to any port 3306
```

#### 7.2 On Application Servers
```bash
# Allow connection to primary
sudo ufw allow out to PRIMARY_IP port 3306

# Allow connection to replicas
sudo ufw allow out to REPLICA_1_IP port 3306
sudo ufw allow out to REPLICA_2_IP port 3306
sudo ufw allow out to REPLICA_3_IP port 3306
```

### Step 8: Backup Strategy

#### 8.1 Primary Database Backup
```bash
# Daily backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root -p'password' --single-transaction eramo > /backups/eramo_$DATE.sql
gzip /backups/eramo_$DATE.sql

# Keep only last 7 days
find /backups -name "eramo_*.sql.gz" -mtime +7 -delete
```

#### 8.2 Automate with Cron
```bash
crontab -e
```

Add:
```bash
0 2 * * * /usr/local/bin/backup_database.sh
```

## AWS RDS Setup (Alternative)

If using AWS RDS, the setup is much simpler:

### 1. Create Read Replica
```bash
aws rds create-db-instance-read-replica \
  --db-instance-identifier myapp-replica-1 \
  --source-db-instance-identifier myapp-primary \
  --db-instance-class db.t3.medium \
  --availability-zone us-east-1b
```

### 2. Get Endpoints
```bash
aws rds describe-db-instances \
  --db-instance-identifier myapp-primary \
  --query 'DBInstances[0].Endpoint.Address'

aws rds describe-db-instances \
  --db-instance-identifier myapp-replica-1 \
  --query 'DBInstances[0].Endpoint.Address'
```

### 3. Update .env
```env
DB_WRITE_HOST=myapp-primary.xxxxx.us-east-1.rds.amazonaws.com
DB_READ_HOST_1=myapp-replica-1.xxxxx.us-east-1.rds.amazonaws.com
```

## Google Cloud SQL Setup (Alternative)

### 1. Create Read Replica
```bash
gcloud sql instances create myapp-replica-1 \
  --master-instance-name=myapp-primary \
  --tier=db-n1-standard-2 \
  --region=us-central1
```

### 2. Get Connection Names
```bash
gcloud sql instances describe myapp-primary --format="value(connectionName)"
gcloud sql instances describe myapp-replica-1 --format="value(connectionName)"
```

### 3. Update .env
```env
DB_WRITE_HOST=PRIMARY_IP
DB_READ_HOST_1=REPLICA_1_IP
```

## Troubleshooting

### Issue: Replication Not Starting
```sql
-- On replica
SHOW SLAVE STATUS\G

-- Look for Last_Error field
-- Common fixes:
STOP SLAVE;
RESET SLAVE;
-- Reconfigure with correct MASTER_LOG_FILE and MASTER_LOG_POS
START SLAVE;
```

### Issue: High Replication Lag
```sql
-- Check replica server load
top

-- Check network latency
ping PRIMARY_SERVER_IP

-- Check binary log size on primary
SHOW BINARY LOGS;

-- Increase replica resources if needed
```

### Issue: Duplicate Key Errors
```sql
-- On replica
STOP SLAVE;
SET GLOBAL SQL_SLAVE_SKIP_COUNTER = 1;
START SLAVE;

-- Or skip the error permanently
SET GLOBAL slave_skip_errors = 1062;
```

### Issue: Application Can't Connect
```bash
# Test connectivity from application server
telnet PRIMARY_IP 3306
telnet REPLICA_1_IP 3306

# Check MySQL user permissions
mysql -h PRIMARY_IP -u root -p -e "SELECT user, host FROM mysql.user;"
```

## Performance Tuning

### Primary Server (Write-Heavy)
```ini
[mysqld]
innodb_buffer_pool_size=4G  # 70-80% of RAM
innodb_log_file_size=512M
innodb_flush_log_at_trx_commit=2
max_connections=500
```

### Replica Servers (Read-Heavy)
```ini
[mysqld]
innodb_buffer_pool_size=6G  # More RAM for caching
read_buffer_size=2M
read_rnd_buffer_size=4M
max_connections=1000  # More connections for reads
```

## Rollback Plan

If issues occur, you can quickly disable read/write splitting:

### 1. Update .env
```env
# Comment out replica hosts
# DB_WRITE_HOST=primary-db.example.com
# DB_READ_HOST_1=replica-1.example.com
# DB_READ_HOST_2=replica-2.example.com
# DB_READ_HOST_3=replica-3.example.com

# Use single host
DB_HOST=primary-db.example.com
```

### 2. Clear Cache
```bash
php artisan config:clear
php artisan config:cache
```

All queries will now use the primary database.

## Success Metrics

After deployment, monitor these metrics:

- ✅ Replication lag < 1 second
- ✅ Primary server CPU < 70%
- ✅ Replica servers distributing load evenly
- ✅ Dashboard load time improved by 30-50%
- ✅ Report generation doesn't impact transactions
- ✅ Can handle 1000+ concurrent users

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Test configuration: `php artisan db:test-splitting --detailed`
3. Monitor replication: `SHOW SLAVE STATUS\G`
4. Review documentation: `DATABASE_READ_WRITE_SPLITTING_COMPLETE.md`
