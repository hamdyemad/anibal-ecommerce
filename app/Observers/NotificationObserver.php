<?php

namespace App\Observers;

use App\Events\AdminNotificationEvent;
use Illuminate\Database\Eloquent\Model;

class NotificationObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->fireNotification($model, 'created');
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        // Check if model has notification config for specific field changes
        if (method_exists($model, 'getNotificationOnUpdate')) {
            $config = $model->getNotificationOnUpdate();
            if ($config && $this->shouldNotifyOnUpdate($model, $config)) {
                $this->fireNotification($model, 'updated', $config);
            }
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->fireNotification($model, 'deleted');
    }

    /**
     * Check if notification should be sent on update
     */
    protected function shouldNotifyOnUpdate(Model $model, array $config): bool
    {
        // Check if specific fields changed
        if (isset($config['watch_fields'])) {
            foreach ($config['watch_fields'] as $field => $values) {
                if ($model->wasChanged($field)) {
                    $newValue = $model->$field;
                    if (in_array($newValue, $values)) {
                        return true;
                    }
                }
            }
            return false;
        }

        return $model->wasChanged();
    }

    /**
     * Fire the notification event
     */
    protected function fireNotification(Model $model, string $action, ?array $updateConfig = null): void
    {
        if (!method_exists($model, 'getNotificationConfig')) {
            return;
        }

        $config = $model->getNotificationConfig($action, $updateConfig);
        
        if (!$config || !($config['enabled'] ?? true)) {
            return;
        }

        event(new AdminNotificationEvent(
            type: $config['type'] ?? "{$action}_" . class_basename($model),
            title: $config['title'] ?? $this->getDefaultTitle($model, $action),
            description: $config['description'] ?? null,
            url: $config['url'] ?? null,
            icon: $config['icon'] ?? $this->getDefaultIcon($action),
            color: $config['color'] ?? $this->getDefaultColor($action),
            notifiable: $model,
            data: $config['data'] ?? null,
            userId: $config['user_id'] ?? null,
            vendorId: $config['vendor_id'] ?? null,
            sendFirebase: $config['send_firebase'] ?? false,
            fcmTokens: $config['fcm_tokens'] ?? null,
        ));
    }

    /**
     * Get default title based on model and action
     */
    protected function getDefaultTitle(Model $model, string $action): string
    {
        $modelName = class_basename($model);
        return ucfirst($action) . ' ' . $modelName . ': ' . ($model->id ?? '');
    }

    /**
     * Get default icon based on action
     */
    protected function getDefaultIcon(string $action): string
    {
        return match($action) {
            'created' => 'uil-plus-circle',
            'updated' => 'uil-edit',
            'deleted' => 'uil-trash-alt',
            default => 'uil-bell',
        };
    }

    /**
     * Get default color based on action
     */
    protected function getDefaultColor(string $action): string
    {
        return match($action) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            default => 'primary',
        };
    }
}
