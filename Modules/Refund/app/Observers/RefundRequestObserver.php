<?php

namespace Modules\Refund\app\Observers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Refund\app\Models\RefundRequest;
use Modules\Refund\app\Services\RefundNotificationService;
use Modules\CatalogManagement\app\Services\StockBookingService;
use Modules\SystemSetting\app\Services\UserPointsService;

class RefundRequestObserver
{
    public function __construct(
        protected RefundNotificationService $notificationService,
        protected StockBookingService $stockBookingService,
        protected UserPointsService $userPointsService
    ) {}

    /**
     * Handle the RefundRequest "created" event.
     */
    public function created(RefundRequest $refundRequest): void
    {
        // Create initial history record for the 'pending' status
        // user_id is null for customer-created refunds (customers are not in users table)
        \Modules\Refund\app\Models\RefundRequestHistory::create([
            'refund_request_id' => $refundRequest->id,
            'old_status' => null,
            'new_status' => $refundRequest->status,
            'user_id' => null,
            'notes' => 'Refund request created by customer',
        ]);
        
        // Notify vendor about new refund request
        if ($refundRequest->vendor_id) {
            $this->notificationService->notifyVendorNewRefund($refundRequest);
        }
        
        // Notify customer about refund creation
        if ($refundRequest->customer_id) {
            $this->notificationService->notifyRefundCreated($refundRequest);
        }
    }

    /**
     * Handle the RefundRequest "updated" event.
     */
    public function updated(RefundRequest $refundRequest): void
    {
        // Handle status change notifications and history
        if ($refundRequest->wasChanged('status')) {
            $oldStatus = $refundRequest->getOriginal('status');
            $newStatus = $refundRequest->status;
            
            // Create history record for status change
            \Modules\Refund\app\Models\RefundRequestHistory::create([
                'refund_request_id' => $refundRequest->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_id' => auth()->id(),
                'notes' => null,
            ]);
            
            // Notify customer about status change
            $this->notificationService->notifyCustomerStatusChange(
                $refundRequest,
                $oldStatus,
                $newStatus
            );
            
            // Notify vendor about status change
            $this->notificationService->notifyVendorStatusChange(
                $refundRequest,
                $oldStatus,
                $newStatus
            );
            
            // Handle refund completion
            if ($newStatus === 'refunded') {
                $this->handleRefundCompletion($refundRequest);
            }
        }
    }

    /**
     * Handle refund completion
     */
    protected function handleRefundCompletion(RefundRequest $refundRequest): void
    {
        DB::transaction(function () use ($refundRequest) {
            $vendor = $refundRequest->vendor;
            $order = $refundRequest->order;
            $customer = $refundRequest->customer;
            
            // 1. Update Customer Points using service
            if ($refundRequest->points_to_deduct > 0) {
                $this->userPointsService->deductPoints(
                    userId: $customer->id,
                    points: $refundRequest->points_to_deduct,
                    transactionableType: RefundRequest::class,
                    transactionableId: $refundRequest->id,
                    description: "Points deducted for refund: {$refundRequest->refund_number}"
                );
            }
            
            if ($refundRequest->points_used > 0) {
                $this->userPointsService->addPoints(
                    userId: $customer->id,
                    points: $refundRequest->points_used,
                    transactionableType: RefundRequest::class,
                    transactionableId: $refundRequest->id,
                    description: "Points refunded for refund: {$refundRequest->refund_number}"
                );
            }
            
            // 2. Update Order - Track Total Refunded Amount
            $order->refunded_amount = ($order->refunded_amount ?? 0) + $refundRequest->total_refund_amount;
            $order->save();
            
            // 3. Reverse Stock Bookings using service
            $orderProductIds = $refundRequest->items->pluck('order_product_id')->toArray();
            $this->stockBookingService->releaseRefundedStock(
                orderId: $order->id,
                orderProductIds: $orderProductIds,
                refundNumber: $refundRequest->refund_number
            );
            
            // 4. Log the refund completion
            $commissionReversed = $this->calculateCommissionReversal($refundRequest);
            
            Log::info('Refund completed', [
                'refund_number' => $refundRequest->refund_number,
                'order_id' => $order->id,
                'vendor_id' => $vendor?->id,
                'total_refund' => $refundRequest->total_refund_amount,
                'commission_reversed' => $commissionReversed,
            ]);
        });
    }

    /**
     * Calculate commission reversal
     */
    protected function calculateCommissionReversal(RefundRequest $refundRequest): float
    {
        $totalCommission = 0;
        
        foreach ($refundRequest->items as $item) {
            $orderProduct = $item->orderProduct;
            
            // Get commission percentage (product or department)
            $commissionPercent = $orderProduct->commission > 0 
                ? $orderProduct->commission 
                : ($orderProduct->vendorProduct->product->department->commission ?? 0);
            
            // Calculate commission on refunded amount (price + shipping)
            $refundableAmount = $item->total_price + $item->shipping_amount;
            $commission = ($refundableAmount * $commissionPercent) / 100;
            
            $totalCommission += $commission;
        }
        
        return $totalCommission;
    }
}
