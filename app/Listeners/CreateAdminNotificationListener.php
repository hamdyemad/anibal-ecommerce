<?php

namespace App\Listeners;

use App\Events\AdminNotificationEvent;
use App\Models\AdminNotification;
use Modules\SystemSetting\app\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class CreateAdminNotificationListener
{
    public function __construct(
        protected FirebaseService $firebaseService
    ) {}

    public function handle(AdminNotificationEvent $event): void
    {
        // Create notification in database
        $notification = AdminNotification::notify(
            type: $event->type,
            title: $event->title,
            description: $event->description,
            url: $event->url,
            icon: $event->icon,
            color: $event->color,
            notifiable: $event->notifiable,
            data: $event->data,
            userId: $event->userId,
            vendorId: $event->vendorId
        );

        // Send Firebase push notification if requested
        if ($event->sendFirebase) {
            $this->sendFirebaseNotification($event, $notification);
        }
    }

    protected function sendFirebaseNotification(AdminNotificationEvent $event, AdminNotification $notification): void
    {
        try {
            $tokens = $event->fcmTokens ?? [];

            // If no specific tokens provided, get tokens based on target
            if (empty($tokens)) {
                $tokens = $this->getTargetFcmTokens($event);
            }

            if (empty($tokens)) {
                return;
            }

            $data = array_merge($event->data ?? [], [
                'notification_id' => (string) $notification->id,
                'type' => $event->type,
                'url' => $event->url,
            ]);

            $result = $this->firebaseService->sendToTokensBatch(
                tokens: $tokens,
                title: $event->title,
                body: $event->description ?? '',
                data: $data
            );

            Log::info('Firebase notification sent', [
                'type' => $event->type,
                'success' => $result['success'],
                'failed' => $result['failed'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification: ' . $e->getMessage());
        }
    }

    protected function getTargetFcmTokens(AdminNotificationEvent $event): array
    {
        $tokens = [];

        // Get vendor FCM tokens if targeting a vendor
        if ($event->vendorId) {
            $vendorTokens = \Modules\Vendor\app\Models\VendorFcmToken::where('vendor_id', $event->vendorId)
                ->pluck('fcm_token')
                ->toArray();
            $tokens = array_merge($tokens, $vendorTokens);
        }

        return $tokens;
    }
}
