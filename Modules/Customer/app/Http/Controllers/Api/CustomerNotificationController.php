<?php

namespace Modules\Customer\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\SystemSetting\app\Services\Api\NotificationApiService;

class CustomerNotificationController extends Controller
{
    use Res;
    
    protected string $userType = 'customer';

    public function __construct(
        protected NotificationApiService $notificationService
    ) {}

    /**
     * Get all notifications for the authenticated customer
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        $perPage = $request->get('per_page', 10);

        $data = $this->notificationService->getNotifications(
            $customer->id,
            $this->userType,
            $perPage
        );

        return $this->sendRes(
            config('responses.data_retrieved')[app()->getLocale()] ?? 'Notifications retrieved successfully',
            true,
            $data
        );
    }

    /**
     * Get a single notification
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $customer = $request->user();

        $notification = $this->notificationService->getNotification(
            $id,
            $customer->id,
            $this->userType
        );

        if (!$notification) {
            return $this->sendRes(
                config('responses.not_found')[app()->getLocale()] ?? 'Notification not found',
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.data_retrieved')[app()->getLocale()] ?? 'Notification retrieved successfully',
            true,
            $notification
        );
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $customer = $request->user();

        $success = $this->notificationService->markAsRead(
            $id,
            $customer->id,
            $this->userType
        );

        if (!$success) {
            return $this->sendRes(
                config('responses.not_found')[app()->getLocale()] ?? 'Notification not found',
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.success')[app()->getLocale()] ?? 'Notification marked as read',
            true
        );
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $customer = $request->user();

        $count = $this->notificationService->markAllAsRead(
            $customer->id,
            $this->userType
        );

        return $this->sendRes(
            config('responses.success')[app()->getLocale()] ?? 'All notifications marked as read',
            true,
            ['marked_count' => $count]
        );
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $customer = $request->user();

        $count = $this->notificationService->getUnreadCount(
            $customer->id,
            $this->userType
        );

        return $this->sendRes(
            config('responses.data_retrieved')[app()->getLocale()] ?? 'Unread count retrieved successfully',
            true,
            ['unread_count' => $count]
        );
    }
}
