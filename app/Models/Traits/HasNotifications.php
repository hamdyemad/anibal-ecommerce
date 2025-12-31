<?php

namespace App\Models\Traits;

use App\Observers\NotificationObserver;

trait HasNotifications
{
    /**
     * Boot the trait
     */
    public static function bootHasNotifications(): void
    {
        static::observe(NotificationObserver::class);
    }

    /**
     * Get notification configuration for an action
     * Override this method in your model to customize notifications
     * 
     * @param string $action The action (created, updated, deleted)
     * @param array|null $updateConfig Config from getNotificationOnUpdate if applicable
     * @return array|null Return null to skip notification
     */
    public function getNotificationConfig(string $action, ?array $updateConfig = null): ?array
    {
        // Default: no notifications
        // Override in model to enable
        return null;
    }

    /**
     * Get notification config for update events
     * Override this method to watch specific field changes
     * 
     * @return array|null
     * Example return:
     * [
     *     'watch_fields' => [
     *         'status' => ['accepted_offer', 'rejected_offer'],
     *     ],
     * ]
     */
    public function getNotificationOnUpdate(): ?array
    {
        return null;
    }
}
