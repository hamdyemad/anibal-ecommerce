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
        $perPage = $request->get('per_page', 15);

        $data = $this->notificationService->getNotifications(
            $customer->id,
            $this->userType,
            $perPage
        );

        return Res::success($data, 'Notifications retrieved successfully');
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
            return Res::error('Notification not found', 404);
        }

        return Res::success($notification, 'Notification retrieved successfully');
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
            return Res::error('Notification not found', 404);
        }

        return Res::success(null, 'Notification marked as read');
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

        return Res::success(['marked_count' => $count], 'All notifications marked as read');
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

        return Res::success(['unread_count' => $count], 'Unread count retrieved successfully');
    }
}
