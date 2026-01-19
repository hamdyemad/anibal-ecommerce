<?php

namespace Modules\Order\app\Observers;

use Modules\Order\app\Models\RequestQuotation;
use App\Services\AdminNotificationService;

class RequestQuotationObserver
{
    public function __construct(
        protected AdminNotificationService $notificationService
    ) {}

    /**
     * Handle the RequestQuotation "created" event.
     */
    public function created(RequestQuotation $quotation): void
    {
        // Create admin notification for new quotation request
        $this->createQuotationNotification($quotation, 'new');
    }

    /**
     * Handle the RequestQuotation "updated" event.
     */
    public function updated(RequestQuotation $quotation): void
    {
        // Create notification when offer is accepted or rejected
        if ($quotation->wasChanged('status')) {
            if ($quotation->status === RequestQuotation::STATUS_ACCEPTED_OFFER) {
                $this->createQuotationNotification($quotation, 'accepted');
            } elseif ($quotation->status === RequestQuotation::STATUS_REJECTED_OFFER) {
                $this->createQuotationNotification($quotation, 'rejected');
            }
        }
    }

    /**
     * Create admin notification for quotation
     */
    protected function createQuotationNotification(RequestQuotation $quotation, string $action): void
    {
        $icon = match($action) {
            'accepted' => 'uil-check-circle',
            'rejected' => 'uil-times-circle',
            default => 'uil-file-question-alt',
        };
        
        $color = match($action) {
            'accepted' => 'success',
            'rejected' => 'danger',
            default => 'warning',
        };
        
        $description = match($action) {
            'accepted' => trans('order::request-quotation.notification_accepted'),
            'rejected' => trans('order::request-quotation.notification_rejected'),
            default => trans('order::request-quotation.notification_new_request'),
        };
        
        $this->notificationService->create(
            type: 'request_quotation_' . $action,
            title: $quotation->customer_name,
            description: $description,
            url: $this->notificationService->generateAdminUrl('admin.request-quotations.index'),
            icon: $icon,
            color: $color,
            notifiable: $quotation,
            data: [
                'quotation_id' => $quotation->id,
                'customer_name' => $quotation->customer_name,
                'status' => $quotation->status,
            ],
            vendorId: null
        );
    }
}
