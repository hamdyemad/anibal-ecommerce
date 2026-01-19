<?php

namespace Modules\Vendor\app\Observers;

use Modules\Vendor\app\Models\Vendor;
use App\Services\AdminNotificationService;

class VendorObserver
{
    public function __construct(
        protected AdminNotificationService $notificationService
    ) {}

    /**
     * Handle the Vendor "created" event.
     */
    public function created(Vendor $vendor): void
    {
        // Create admin notification for new vendor
        $this->createVendorNotification($vendor);
    }

    /**
     * Handle the Vendor "updated" event.
     */
    public function updated(Vendor $vendor): void
    {
        // If vendor was just activated, mark notification as read
        if ($vendor->wasChanged('active') && $vendor->active == 1) {
            $this->notificationService->markTypeAsRead('vendor', $vendor);
        }
    }

    /**
     * Create admin notification for vendor
     */
    protected function createVendorNotification(Vendor $vendor): void
    {
        // Determine icon and color based on active status
        $icon = $vendor->active == 1 ? 'uil-store' : 'uil-user-plus';
        $color = $vendor->active == 1 ? 'success' : 'warning';
        $description = $vendor->active == 1 
            ? 'New vendor registered' 
            : trans('menu.become a vendor requests.wants_to_become');
        
        $this->notificationService->create(
            type: 'vendor',
            title: $vendor->name,
            description: $description,
            url: $this->notificationService->generateAdminUrl('admin.vendors.show', ['vendor' => $vendor]),
            icon: $icon,
            color: $color,
            notifiable: $vendor,
            data: [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'active' => $vendor->active,
            ],
            vendorId: null
        );
    }
}
