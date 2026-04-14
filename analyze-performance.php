<?php
/**
 * Performance Log Analyzer
 * Run: php analyze-performance.php
 */

$logFile = __DIR__ . '/storage/logs/performance-' . date('Y-m-d') . '.log';

if (!file_exists($logFile)) {
    // Try without date suffix
    $logFile = __DIR__ . '/storage/logs/performance.log';
    if (!file_exists($logFile)) {
        echo "❌ Performance log not found. Please visit http://127.0.0.1:8000 first.\n";
        echo "Looking for: $logFile\n";
        exit(1);
    }
}

echo "=== LOGIN PAGE PERFORMANCE ANALYSIS ===\n\n";

// Read the last 100 lines
$lines = array_slice(file($logFile), -100);

$loginRequests = [];

foreach ($lines as $line) {
    if (strpos($line, 'DETAILED PERFORMANCE PROFILE') !== false) {
        // Parse JSON from log line
        preg_match('/\{.*\}$/s', $line, $matches);
        if (!empty($matches[0])) {
            $data = json_decode($matches[0], true);
            if ($data && isset($data['url']) && strpos($data['url'], '127.0.0.1:8000') !== false) {
                $loginRequests[] = $data;
            }
        }
    }
}

if (empty($loginRequests)) {
    echo "❌ No login page requests found in log.\n";
    echo "Please visit http://127.0.0.1:8000 and try again.\n";
    exit(1);
}

// Analyze the most recent request
$latest = end($loginRequests);

echo "📊 Latest Request Analysis:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "🌐 URL: " . $latest['url'] . "\n";
echo "⏱️  Total Time: " . $latest['total_time_ms'] . " ms\n";
echo "🔍 Total Queries: " . $latest['total_queries'] . "\n\n";

echo "📈 Query Breakdown:\n";
foreach ($latest['query_stats'] as $type => $count) {
    if ($count > 0) {
        echo "   • " . strtoupper($type) . ": $count\n";
    }
}
echo "\n";

echo "⏲️  Execution Timeline:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
foreach ($latest['checkpoints'] as $checkpoint) {
    $duration = $checkpoint['duration_ms'];
    $bar = str_repeat('█', min(50, (int)($duration / 10)));
    
    printf("%-30s %8.2f ms %s\n", 
        $checkpoint['from'] . ' → ' . $checkpoint['to'],
        $duration,
        $bar
    );
}
echo "\n";

if (!empty($latest['slow_queries'])) {
    echo "🐌 Slow Queries (>10ms):\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    foreach ($latest['slow_queries'] as $query) {
        echo "   ⚠️  " . $query['time'] . " ms: " . substr($query['query'], 0, 100) . "...\n";
    }
    echo "\n";
}

// Find the slowest checkpoint
$slowest = null;
$slowestTime = 0;
foreach ($latest['checkpoints'] as $checkpoint) {
    if ($checkpoint['duration_ms'] > $slowestTime) {
        $slowestTime = $checkpoint['duration_ms'];
        $slowest = $checkpoint;
    }
}

echo "🎯 Performance Bottleneck:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "The slowest part is: " . $slowest['from'] . " → " . $slowest['to'] . "\n";
echo "Duration: " . $slowest['duration_ms'] . " ms (" . round(($slowest['duration_ms'] / $latest['total_time_ms']) * 100, 1) . "% of total time)\n\n";

// Recommendations
echo "💡 Recommendations:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if ($latest['total_queries'] > 5) {
    echo "   ⚠️  High query count (" . $latest['total_queries'] . "). Consider eager loading or caching.\n";
}

if (!empty($latest['slow_queries'])) {
    echo "   ⚠️  Found " . count($latest['slow_queries']) . " slow queries. Add indexes or optimize them.\n";
}

if ($slowestTime > 500) {
    echo "   ⚠️  Bottleneck takes " . $slowestTime . "ms. This needs optimization.\n";
}

if ($latest['total_time_ms'] < 500) {
    echo "   ✅ Performance is good! Total time under 500ms.\n";
} elseif ($latest['total_time_ms'] < 1000) {
    echo "   ⚠️  Performance is acceptable but could be better.\n";
} else {
    echo "   ❌ Performance is poor. Needs immediate optimization.\n";
}

echo "\n";
echo "📝 Full query details are in: storage/logs/performance.log\n";
