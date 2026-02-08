<?php

namespace Modules\Order\app\Observers;

use Modules\Order\app\Models\RequestQuotationVendor;
use Modules\Order\app\Services\RequestQuotationNotificationService;

class RequestQuotationVendorObserver
{
    public function __construct(
        protected RequestQuotationNotificationService $notificationService
    ) {}

    /**
     * Handle the RequestQuotationVendor "updated" event.
     */
    public function updated(RequestQuotationVendor $quotationVendor): void
    {
        // Check if status was changed
        if ($quotationVendor->wasChanged('status')) {
            $oldStatus = $quotationVendor->getOriginal('status');
            $newStatus = $quotationVendor->status;

            // Handle status change notifications
            $this->handleStatusChange($quotationVendor, $oldStatus, $newStatus);
        }
    }

    /**
     * Handle status change and send appropriate notifications
     */
    protected function handleStatusChange(RequestQuotationVendor $quotationVendor, ?string $oldStatus, string $newStatus): void
    {
        switch ($newStatus) {
            case RequestQuotationVendor::STATUS_OFFER_SENT:
                // Vendor sent offer to customer
                $this->notificationService->notifyCustomerOfferReceived($quotationVendor);
                break;

            case RequestQuotationVendor::STATUS_OFFER_ACCEPTED:
                // Customer accepted vendor's offer
                $this->notificationService->notifyVendorOfferAccepted($quotationVendor);
                break;

            case RequestQuotationVendor::STATUS_OFFER_REJECTED:
                // Customer rejected vendor's offer
                $this->notificationService->notifyVendorOfferRejected($quotationVendor);
                break;

            case RequestQuotationVendor::STATUS_ORDER_CREATED:
                // Order was created from accepted offer
                $this->notificationService->notifyCustomerOrderCreated($quotationVendor);
                break;
        }
    }
}
