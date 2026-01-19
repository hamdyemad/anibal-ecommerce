<?php

namespace Modules\Order\app\Observers;

use Modules\Order\app\Models\Order;
use App\Services\AdminNotificationService;

class OrderObserver
{
    public function __construct(
        protected AdminNotificationService $notificationService
    ) {}

    /**
     * Handle the Order "created" event.
     * Vendor order stages are now created by OrderProductObserver when products are added.
     */
    public function created(Order $order): void
    {
        // Create admin notification for new order
        $this->createOrderNotification($order);
    }

    /**
     * Handle the Order "updated" event.
     * Stock bookings and points are now handled at vendor level via VendorOrderStageObserver
     */
    public function updated(Order $order): void
    {
        // Stage changes are handled at vendor level via VendorOrderStageObserver
    }

    /**
     * Create admin notification for new order
     */
    protected function createOrderNotification(Order $order): void
    {
        // Get all vendors involved in this order
        $vendorIds = $order->products()->distinct()->pluck('vendor_id')->toArray();
        
        // Create notification for each vendor
        foreach ($vendorIds as $vendorId) {
            $this->notificationService->create(
                type: 'new_order',
                title: trans('menu.order') . ' #' . $order->order_number,
                description: $order->customer_name ?? 'New order received',
                url: $this->notificationService->generateAdminUrl('admin.orders.show', ['order' => $order]),
                icon: 'uil-shopping-bag',
                color: 'primary',
                notifiable: $order,
                data: [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $order->customer_id,
                    'total_amount' => $order->total,
                ],
                vendorId: $vendorId
            );
        }
        
        // Create notification for admin
        if (count($vendorIds) > 0) {
            $this->notificationService->create(
                type: 'new_order',
                title: trans('menu.order') . ' #' . $order->order_number,
                description: $order->customer_name ?? 'New order received',
                url: $this->notificationService->generateAdminUrl('admin.orders.show', ['order' => $order]),
                icon: 'uil-shopping-bag',
                color: 'primary',
                notifiable: $order,
                data: [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $order->customer_id,
                    'total_amount' => $order->total,
                ],
                vendorId: null
            );
        }
    }
}
