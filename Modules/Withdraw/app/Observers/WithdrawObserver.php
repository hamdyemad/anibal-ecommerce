<?php

namespace Modules\Withdraw\app\Observers;

use Modules\Withdraw\app\Models\Withdraw;
use App\Services\AdminNotificationService;

class WithdrawObserver
{
    public function __construct(protected AdminNotificationService $notificationService)
    {
    }

    /**
     * Handle the Withdraw "created" event.
     */
    public function created(Withdraw $withdraw): void
    {
        // Create admin notification for new withdraw request
        if ($withdraw->status === 'new') {
            $this->createWithdrawRequestNotification($withdraw);
        }
    }

    /**
     * Handle the Withdraw "updated" event.
     */
    public function updated(Withdraw $withdraw): void
    {
        // If withdraw status changed to accepted or rejected, create notification for vendor
        if ($withdraw->wasChanged('status') && in_array($withdraw->status, ['accepted', 'rejected'])) {
            $this->createWithdrawStatusNotification($withdraw);
        }
    }

    /**
     * Create admin notification for new withdraw request
     */
    protected function createWithdrawRequestNotification(Withdraw $withdraw): void
    {
        $this->notificationService->create(
            type: 'withdraw_request',
            title: $withdraw->vendor?->name ?? 'Vendor',
            description: trans('menu.withdraw module.vendor_sent_request', ['vendor' => $withdraw->vendor?->name ?? 'Vendor']),
            url: $this->notificationService->generateAdminUrl('admin.transactionsRequests', ['status' => 'new']),
            icon: 'uil-wallet',
            color: 'warning',
            notifiable: $withdraw,
            data: [
                'withdraw_id' => $withdraw->id,
                'vendor_id' => $withdraw->reciever_id,
                'vendor_name' => $withdraw->vendor?->name,
                'amount' => $withdraw->sent_amount,
            ],
            vendorId: null // For admin only
        );
    }

    /**
     * Create vendor notification for withdraw status change
     */
    protected function createWithdrawStatusNotification(Withdraw $withdraw): void
    {
        $isAccepted = $withdraw->status === 'accepted';
        
        $this->notificationService->create(
            type: 'withdraw_status',
            title: $isAccepted 
                ? trans('menu.withdraw module.bnaia_sent_money') 
                : trans('menu.withdraw module.bnaia_rejected_request'),
            description: trans('menu.withdraw module.request_value') . ': ' . $withdraw->sent_amount . ' ' . currency(),
            url: $this->notificationService->generateAdminUrl('admin.transactionsRequests', [
                'status' => $isAccepted ? 'accepted' : 'rejected'
            ]),
            icon: 'uil-wallet',
            color: $isAccepted ? 'success' : 'danger',
            notifiable: $withdraw,
            data: [
                'withdraw_id' => $withdraw->id,
                'status' => $withdraw->status,
                'amount' => $withdraw->sent_amount,
            ],
            vendorId: $withdraw->reciever_id // For specific vendor
        );
    }
}
