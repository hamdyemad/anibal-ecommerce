<?php

namespace Modules\SystemSetting\app\Interfaces;

use Modules\SystemSetting\app\Models\PushNotification;

interface PushNotificationRepositoryInterface
{
    public function all(array $filters = []);

    public function find($id);

    public function createAndSend(array $data): PushNotification;

    public function delete($id);
}
