<?php

namespace Modules\Refund\app\Observers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Refund\app\Models\RefundRequest;
use Modules\Refund\app\Services\RefundNotificationService;
use Modules\CatalogManagement\app\Services\StockBookingService;
use Modules\SystemSetting\app\Services\UserPointsService;

class RefundRequestObserver
{
    // Flag to prevent infinite loop when updating refunded_at
    protected static $isProcessingRefund = false;
    
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
            'notes' => 'refund::refund.history.created_by_customer', // Translation key
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
        // Prevent infinite loop when updating refunded_at
        if (self::$isProcessingRefund) {
            return;
        }
        
        // Handle status change notifications and history
        if ($refundRequest->wasChanged('status')) {
            $oldStatus = $refundRequest->getOriginal('status');
            $newStatus = $refundRequest->status;
            
            // Determine notes based on status change
            $notes = $this->getStatusChangeNotes($newStatus, $refundRequest);
            
            // Create history record for status change
            \Modules\Refund\app\Models\RefundRequestHistory::create([
                'refund_request_id' => $refundRequest->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_id' => auth()->id(),
                'notes' => $notes,
            ]);
            
            // Notify customer about status change
            try {
                $this->notificationService->notifyCustomerStatusChange(
                    $refundRequest,
                    $oldStatus,
                    $newStatus
                );
            } catch (\Exception $e) {
                Log::error('Failed to notify customer about refund status change', [
                    'refund_id' => $refundRequest->id,
                    'error' => $e->getMessage(),
                ]);
            }
            
            // Notify vendor about status change
            try {
                $this->notificationService->notifyVendorStatusChange(
                    $refundRequest,
                    $oldStatus,
                    $newStatus
                );
            } catch (\Exception $e) {
                Log::error('Failed to notify vendor about refund status change', [
                    'refund_id' => $refundRequest->id,
                    'error' => $e->getMessage(),
                ]);
            }
            
            // Handle refund completion
            if ($newStatus === 'refunded') {
                $this->handleRefundCompletion($refundRequest);
            }
        }
    }

    /**
     * Get notes for status change based on new status and refund data
     */
    protected function getStatusChangeNotes(string $newStatus, RefundRequest $refundRequest): ?string
    {
        // For cancelled status, include the cancellation reason from vendor_notes
        if ($newStatus === 'cancelled' && $refundRequest->vendor_notes) {
            return $refundRequest->vendor_notes;
        }
        
        // For other statuses, use translation keys
        return match($newStatus) {
            'approved' => 'refund::refund.history.status_changed_to_approved',
            'in_progress' => 'refund::refund.history.status_changed_to_in_progress',
            'picked_up' => 'refund::refund.history.status_changed_to_picked_up',
            'refunded' => 'refund::refund.history.status_changed_to_refunded',
            default => null,
        };
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
            // Deduct points that were earned from this purchase (if any)
            // if ($refundRequest->points_to_deduct > 0) {
            //     $this->userPointsService->deductPoints(
            //         userId: $customer->user_id,
            //         points: $refundRequest->points_to_deduct,
            //         transactionableType: RefundRequest::class,
            //         transactionableId: $refundRequest->id,
            //         description: "Points deducted for refund: {$refundRequest->refund_number}"
            //     );
            // }
            
            // // Return points that were used in the original purchase
            // if ($refundRequest->points_used > 0) {
            //     $this->userPointsService->addPoints(
            //         userId: $customer->user_id,
            //         points: $refundRequest->points_used,
            //         transactionableType: RefundRequest::class,
            //         transactionableId: $refundRequest->id,
            //         description: "Points refunded for refund: {$refundRequest->refund_number}"
            //     );
            // }
            
            // // 3. Reverse Stock Bookings using service
            // $orderProductIds = $refundRequest->items->pluck('order_product_id')->toArray();
            // $this->stockBookingService->releaseRefundedStock(
            //     orderId: $order->id,
            //     orderProductIds: $orderProductIds,
            //     refundNumber: $refundRequest->refund_number
            // );
            
            // // 4. Create Accounting Entry for Refund
            // // This will reduce vendor's balance
            // $commissionDetails = $this->calculateCommissionReversal($refundRequest);
            
            // \Modules\Accounting\app\Models\AccountingEntry::create([
            //     'order_id' => $order->id,
            //     'vendor_id' => $vendor?->id,
            //     'type' => 'refund',
            //     'amount' => $refundRequest->total_refund_amount,
            //     'commission_rate' => 0, // Will be calculated from items
            //     'commission_amount' => $commissionDetails['total_commission'],
            //     'vendor_amount' => $refundRequest->total_refund_amount - $commissionDetails['total_commission'],
            //     'description' => "Refund for order {$order->order_number} - {$refundRequest->refund_number}",
            //     'metadata' => [
            //         'refund_request_id' => $refundRequest->id,
            //         'refund_number' => $refundRequest->refund_number,
            //         'refund_reason' => $refundRequest->reason,
            //         'products_amount' => $refundRequest->total_products_amount,
            //         'shipping_amount' => $refundRequest->total_shipping_amount,
            //         'tax_amount' => $refundRequest->total_tax_amount,
            //         'commission_details' => $commissionDetails['items'],
            //     ],
            // ]);
            
            // // 5. Create Payment Refund Record (if order was paid online)
            // if ($order->payment_type === 'online' && $order->payment_visa_status === 'success') {
            //     $latestPayment = $order->latestPayment;
                
            //     if ($latestPayment && $latestPayment->status === 'paid') {
            //         // Create refund payment record
            //         \Modules\Order\app\Models\Payment::create([
            //             'order_id' => $order->id,
            //             'paymob_order_id' => $latestPayment->paymob_order_id,
            //             'payment_method' => $latestPayment->payment_method,
            //             'amount_cents' => (int) ($refundRequest->total_refund_amount * 100),
            //             'status' => 'refunded',
            //             'transaction_id' => 'REFUND-' . $refundRequest->refund_number,
            //             'payment_data' => [
            //                 'refund_request_id' => $refundRequest->id,
            //                 'refund_number' => $refundRequest->refund_number,
            //                 'original_payment_id' => $latestPayment->id,
            //                 'refund_reason' => $refundRequest->reason,
            //             ],
            //         ]);
            //     }
            // }
        });
    }

    /**
     * Calculate commission reversal with detailed breakdown
     */
    protected function calculateCommissionReversal(RefundRequest $refundRequest): array
    {
        $totalCommission = 0;
        $itemsDetails = [];
        
        foreach ($refundRequest->items as $item) {
            $orderProduct = $item->orderProduct;
            
            if (!$orderProduct) {
                continue;
            }
            
            // Get commission percentage from order_product (already stored there)
            $commissionPercent = $orderProduct->commission ?? 0;
            
            // If commission is 0, try to get it from department
            if ($commissionPercent == 0 && $orderProduct->vendorProduct) {
                $commissionPercent = $orderProduct->vendorProduct->product->department->commission ?? 0;
            }
            
            // Calculate commission on refunded amount
            // Commission is calculated on (price + shipping) including tax
            $refundableAmount = $item->total_price + $item->shipping_amount;
            $commission = round(($refundableAmount * $commissionPercent) / 100, 2);
            
            $totalCommission += $commission;
            
            $itemsDetails[] = [
                'order_product_id' => $orderProduct->id,
                'product_name' => $orderProduct->name,
                'quantity' => $item->quantity,
                'price' => $item->total_price,
                'shipping' => $item->shipping_amount,
                'refundable_amount' => $refundableAmount,
                'commission_percent' => $commissionPercent,
                'commission_amount' => $commission,
            ];
        }
        
        return [
            'total_commission' => round($totalCommission, 2),
            'items' => $itemsDetails,
        ];
    }
}
