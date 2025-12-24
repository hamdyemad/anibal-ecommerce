<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Interfaces\PushNotificationRepositoryInterface;
use Modules\SystemSetting\app\Models\PushNotification;

class PushNotificationService
{
    protected PushNotificationRepositoryInterface $repository;

    public function __construct(PushNotificationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllNotifications(array $filters = [])
    {
        return $this->repository->all($filters);
    }

    public function getNotificationById($id)
    {
        return $this->repository->find($id);
    }

    public function createAndSend(array $data): PushNotification
    {
        return $this->repository->createAndSend($data);
    }

    public function deleteNotification($id)
    {
        return $this->repository->delete($id);
    }
}
