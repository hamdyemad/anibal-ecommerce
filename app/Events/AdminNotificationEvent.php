<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class AdminNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $type,
        public string $title,
        public ?string $description = null,
        public ?string $url = null,
        public string $icon = 'uil-bell',
        public string $color = 'primary',
        public ?Model $notifiable = null,
        public ?array $data = null,
        public ?int $userId = null,        // Target specific admin user
        public ?int $vendorId = null,      // Target specific vendor
        public bool $sendFirebase = false, // Send Firebase push notification
        public ?array $fcmTokens = null,   // Specific FCM tokens to send to
    ) {}
}
