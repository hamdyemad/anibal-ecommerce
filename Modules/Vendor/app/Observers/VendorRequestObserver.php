<?php

namespace Modules\Vendor\app\Observers;

use Modules\Vendor\app\Models\VendorRequest;
use App\Services\AdminNotificationService;

class VendorRequestObserver
{
    public function __construct(
        protected AdminNotificationService $notificationService
    ) {}

    /**
     * Handle the VendorRequest "created" event.
     */
    public function created(VendorRequest $vendorRequest): void
    {
        // Create admin notification for new vendor request
        if ($vendorRequest->status === 'pending') {
            try {
                $this->createVendorRequestNotification($vendorRequest);
            } catch (\Exception $e) {
                // Log error but don't break the vendor request creation
                \Log::error('Failed to create vendor request notification', [
                    'vendor_request_id' => $vendorRequest->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle the VendorRequest "updated" event.
     */
    public function updated(VendorRequest $vendorRequest): void
    {
        // If vendor request status changed from pending, mark notification as read
        if ($vendorRequest->wasChanged('status') && $vendorRequest->status !== 'pending') {
            $this->notificationService->markTypeAsRead('vendor_request', $vendorRequest);
        }
    }

    /**
     * Create admin notification for vendor request
     */
    protected function createVendorRequestNotification(VendorRequest $vendorRequest): void
    {
        $this->notificationService->create(
            type: 'vendor_request',
            title: 'menu.become a vendor requests.new_request', // Translation key for title
            description: 'menu.become a vendor requests.wants_to_become', // Translation key for description
            url: $this->notificationService->generateAdminUrl('admin.vendor-requests.index'),
            icon: 'uil-user-plus',
            color: 'warning',
            notifiable: $vendorRequest,
            data: [
                'menu.become a vendor requests.vendor_request_id' => $vendorRequest->id,
                'common.company_name' => $vendorRequest->company_name,
                'common.email' => $vendorRequest->email,
            ],
            vendorId: null
        );
    }
}
