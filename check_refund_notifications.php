<?php
// Temporary debug script to check refund notifications
// Run this from the project root: php check_refund_notifications.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking refund notifications...\n\n";

// Get all refund notifications
$refundNotifications = \App\Models\AdminNotification::whereIn('type', ['new_refund_request', 'refund_status_changed'])
    ->orderBy('created_at', 'desc')
    ->get();

echo "Total refund notifications: " . $refundNotifications->count() . "\n\n";

foreach ($refundNotifications as $notification) {
    echo "ID: {$notification->id}\n";
    echo "Type: {$notification->type}\n";
    echo "Vendor ID: " . ($notification->vendor_id ?? 'NULL') . "\n";
    echo "Country ID: " . ($notification->country_id ?? 'NULL') . "\n";
    echo "Created: {$notification->created_at}\n";
    echo "Title: {$notification->title}\n";
    echo "---\n\n";
}
