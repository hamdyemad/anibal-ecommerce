<?php

namespace App\Services;

use App\Models\AdminNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AdminNotificationService
{
    /**
     * Generate admin route URL with locale and country code
     */
    public function generateAdminUrl(string $routeName, array $parameters = []): string
    {
        try {
            // Get locale and country code from session or use defaults
            $lang = app()->getLocale() ?? 'en';
            $countryCode = strtolower(session('country_code', 'eg'));
            
            // Merge with provided parameters
            $parameters = array_merge([
                'lang' => $lang,
                'countryCode' => $countryCode,
            ], $parameters);
            
            return route($routeName, $parameters);
        } catch (\Exception $e) {
            \Log::error('Failed to generate admin URL', [
                'route' => $routeName,
                'error' => $e->getMessage(),
            ]);
            // Return a fallback URL
            return url('/');
        }
    }

    /**
     * Create a new admin notification
     */
    public function create(
        string $type,
        string $title,
        ?string $description = null,
        ?string $url = null,
        string $icon = 'uil-bell',
        string $color = 'primary',
        ?Model $notifiable = null,
        ?array $data = null,
        ?int $userId = null,
        ?int $vendorId = null
    ): ?AdminNotification {
        try {
            return AdminNotification::notify(
                type: $type,
                title: $title,
                description: $description, // Store translation key directly
                url: $url,
                icon: $icon,
                color: $color,
                notifiable: $notifiable,
                data: $data,
                userId: $userId,
                vendorId: $vendorId
            );
        } catch (\Exception $e) {
            Log::error('Failed to create admin notification', [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        try {
            $notification = AdminNotification::find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', [
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Mark notifications by type and notifiable as read
     */
    public function markTypeAsRead(string $type, Model $notifiable): int
    {
        try {
            return AdminNotification::where('type', $type)
                ->where('notifiable_type', get_class($notifiable))
                ->where('notifiable_id', $notifiable->id)
                ->update(['is_read' => true, 'read_at' => now()]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notifications as read', [
                'type' => $type,
                'notifiable' => get_class($notifiable),
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Mark notification as viewed by user
     */
    public function markAsViewedBy(int $notificationId, int $userId): bool
    {
        try {
            $notification = AdminNotification::find($notificationId);
            if ($notification) {
                $notification->markAsViewedBy($userId);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as viewed', [
                'notification_id' => $notificationId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
