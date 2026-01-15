<?php

namespace Modules\SystemSetting\app\Interfaces\Api;

interface NotificationApiRepositoryInterface
{
    /**
     * Get notifications for a user
     */
    public function getNotifications(int $userId, string $userType, int $perPage = 15): array;

    /**
     * Get a single notification
     */
    public function getNotification(int $notificationId, int $userId, string $userType): ?array;

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $userId, string $userType): bool;

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(int $userId, string $userType): int;

    /**
     * Get unread count
     */
    public function getUnreadCount(int $userId, string $userType): int;
}
