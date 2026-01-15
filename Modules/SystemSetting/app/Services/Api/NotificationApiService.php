<?php

namespace Modules\SystemSetting\app\Services\Api;

use Modules\SystemSetting\app\Interfaces\Api\NotificationApiRepositoryInterface;

class NotificationApiService
{
    public function __construct(
        protected NotificationApiRepositoryInterface $repository
    ) {}

    /**
     * Get notifications for a user
     */
    public function getNotifications(int $userId, string $userType, int $perPage = 15): array
    {
        return $this->repository->getNotifications($userId, $userType, $perPage);
    }

    /**
     * Get a single notification
     */
    public function getNotification(int $notificationId, int $userId, string $userType): ?array
    {
        return $this->repository->getNotification($notificationId, $userId, $userType);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $userId, string $userType): bool
    {
        return $this->repository->markAsRead($notificationId, $userId, $userType);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(int $userId, string $userType): int
    {
        return $this->repository->markAllAsRead($userId, $userType);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(int $userId, string $userType): int
    {
        return $this->repository->getUnreadCount($userId, $userType);
    }
}
