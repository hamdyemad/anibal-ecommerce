<?php

namespace Modules\SystemSetting\app\Repositories\Api;

use Illuminate\Support\Facades\DB;
use Modules\SystemSetting\app\Interfaces\Api\NotificationApiRepositoryInterface;
use Modules\SystemSetting\app\Models\PushNotification;

class NotificationApiRepository implements NotificationApiRepositoryInterface
{
    protected string $lang;

    public function __construct()
    {
        $this->lang = request()->header('Accept-Language', app()->getLocale());
    }

    /**
     * Get notifications for a user
     */
    public function getNotifications(int $userId, string $userType, int $perPage = 15): array
    {
        $query = $this->buildNotificationQuery($userId, $userType);
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $viewedIds = $this->getViewedIds($userId, $userType);

        $data = $notifications->getCollection()->map(function ($notification) use ($viewedIds) {
            return $this->transformNotification($notification, $viewedIds);
        });

        return [
            'notifications' => $data,
            'unread_count' => $this->getUnreadCount($userId, $userType),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ];
    }

    /**
     * Get a single notification
     */
    public function getNotification(int $notificationId, int $userId, string $userType): ?array
    {
        $query = $this->buildNotificationQuery($userId, $userType);
        $notification = $query->where('push_notifications.id', $notificationId)->first();

        if (!$notification) {
            return null;
        }

        $viewedIds = $this->getViewedIds($userId, $userType);

        return $this->transformNotification($notification, $viewedIds);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $userId, string $userType): bool
    {
        // Verify notification exists for this user
        $query = $this->buildNotificationQuery($userId, $userType);
        $exists = $query->where('push_notifications.id', $notificationId)->exists();

        if (!$exists) {
            return false;
        }

        $table = $this->getViewsTable($userType);
        $column = $this->getUserColumn($userType);

        DB::table($table)->updateOrInsert(
            [
                'push_notification_id' => $notificationId,
                $column => $userId,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return true;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(int $userId, string $userType): int
    {
        $query = $this->buildNotificationQuery($userId, $userType);
        $notificationIds = $query->pluck('push_notifications.id')->toArray();

        $viewedIds = $this->getViewedIds($userId, $userType);
        $unreadIds = array_diff($notificationIds, $viewedIds);

        if (empty($unreadIds)) {
            return 0;
        }

        $table = $this->getViewsTable($userType);
        $column = $this->getUserColumn($userType);

        $insertData = array_map(function ($notificationId) use ($userId, $column) {
            return [
                'push_notification_id' => $notificationId,
                $column => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $unreadIds);

        DB::table($table)->insert($insertData);

        return count($unreadIds);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(int $userId, string $userType): int
    {
        $viewedIds = $this->getViewedIds($userId, $userType);

        $query = $this->buildNotificationQuery($userId, $userType);

        if (!empty($viewedIds)) {
            $query->whereNotIn('push_notifications.id', $viewedIds);
        }

        return $query->count();
    }

    /**
     * Build the base notification query based on user type
     */
    protected function buildNotificationQuery(int $userId, string $userType)
    {
        $query = PushNotification::query();

        switch ($userType) {
            case 'customer':
                $query->where(function ($q) use ($userId) {
                    $q->where('type', PushNotification::TYPE_ALL)
                        ->orWhere(function ($subQ) use ($userId) {
                            $subQ->where('type', PushNotification::TYPE_SPECIFIC)
                                ->whereHas('customers', function ($customerQ) use ($userId) {
                                    $customerQ->where('customer_id', $userId);
                                });
                        });
                });
                break;

            case 'vendor':
                $query->where(function ($q) use ($userId) {
                    $q->where('type', PushNotification::TYPE_ALL_VENDORS)
                        ->orWhere(function ($subQ) use ($userId) {
                            $subQ->where('type', PushNotification::TYPE_SPECIFIC_VENDORS)
                                ->whereHas('vendors', function ($vendorQ) use ($userId) {
                                    $vendorQ->where('vendor_id', $userId);
                                });
                        });
                });
                break;

            default:
                $query->where('type', PushNotification::TYPE_ALL);
        }

        return $query;
    }

    /**
     * Get viewed notification IDs for a user
     */
    protected function getViewedIds(int $userId, string $userType): array
    {
        $table = $this->getViewsTable($userType);
        $column = $this->getUserColumn($userType);

        return DB::table($table)
            ->where($column, $userId)
            ->pluck('push_notification_id')
            ->toArray();
    }

    /**
     * Get the views table name based on user type
     */
    protected function getViewsTable(string $userType): string
    {
        return match ($userType) {
            'customer' => 'push_notification_customer_views',
            'vendor' => 'push_notification_vendor_views',
            default => 'push_notification_views',
        };
    }

    /**
     * Get the user column name based on user type
     */
    protected function getUserColumn(string $userType): string
    {
        return match ($userType) {
            'customer' => 'customer_id',
            'vendor' => 'vendor_id',
            default => 'user_id',
        };
    }

    /**
     * Transform notification to array
     */
    protected function transformNotification(PushNotification $notification, array $viewedIds): array
    {
        return [
            'id' => $notification->id,
            'title' => $notification->getTranslation('title', $this->lang) 
                ?? $notification->getTranslation('title', 'en'),
            'description' => $notification->getTranslation('description', $this->lang) 
                ?? $notification->getTranslation('description', 'en'),
            'image' => $notification->image ? asset('storage/' . $notification->image) : null,
            'is_read' => in_array($notification->id, $viewedIds),
            'created_at' => $notification->getRawOriginal('created_at'),
        ];
    }
}
