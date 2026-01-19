<?php

namespace Modules\Refund\app\Helpers;

use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\Refund\app\Models\VendorRefundSetting;

class RefundHelper
{
    /**
     * Get refund days for a vendor product
     * Returns product-specific refund days if set, otherwise vendor-specific
     * 
     * @param VendorProduct $vendorProduct
     * @return int Number of days allowed for refund
     */
    public static function getRefundDays(?VendorProduct $vendorProduct): int
    {
        // If no vendor product provided, return default
        if (!$vendorProduct) {
            return 7;
        }

        // Priority 1: If vendor product has specific refund days set, use it
        if ($vendorProduct->refund_days !== null && $vendorProduct->refund_days > 0) {
            return $vendorProduct->refund_days;
        }

        // Priority 2: Use vendor-specific settings
        if ($vendorProduct->vendor_id) {
            $vendorSettings = VendorRefundSetting::getForVendor($vendorProduct->vendor_id);
            return $vendorSettings->refund_processing_days;
        }

        // Default fallback
        return 7;
    }

    /**
     * Check if a vendor product is eligible for refund based on delivery date
     * 
     * @param VendorProduct $vendorProduct
     * @param \Carbon\Carbon|string|null $deliveredAt The delivery date
     * @return bool
     */
    public static function isEligibleForRefund(?VendorProduct $vendorProduct, $deliveredAt = null): bool
    {
        // If no vendor product provided, default to false
        if (!$vendorProduct) {
            return false;
        }

        // Check if refunds are enabled for this product
        if (!$vendorProduct->is_able_to_refund) {
            return false;
        }

        // If no delivery date provided, it's not eligible for the refund window yet
        if (!$deliveredAt) {
            return false;
        }

        // Convert to Carbon instance if string
        $deliveredAt = $deliveredAt instanceof \Carbon\Carbon 
            ? $deliveredAt 
            : \Carbon\Carbon::parse($deliveredAt);

        // Get refund days (product-specific or system default)
        $refundDays = self::getRefundDays($vendorProduct);

        // Check if within refund window
        $refundDeadline = $deliveredAt->copy()->addDays($refundDays);
        
        return now()->lte($refundDeadline);
    }

    /**
     * Get the refund deadline for a vendor product based on delivery date
     * 
     * @param VendorProduct $vendorProduct
     * @param \Carbon\Carbon|string $deliveredAt The delivery date
     * @return \Carbon\Carbon
     */
    public static function getRefundDeadline(?VendorProduct $vendorProduct, $deliveredAt): ?\Carbon\Carbon
    {
        if (!$vendorProduct || !$deliveredAt) {
            return null;
        }

        $deliveredAt = $deliveredAt instanceof \Carbon\Carbon 
            ? $deliveredAt 
            : \Carbon\Carbon::parse($deliveredAt);

        $refundDays = self::getRefundDays($vendorProduct);

        return $deliveredAt->copy()->addDays($refundDays);
    }

    /**
     * Get remaining days to request refund
     * 
     * @param VendorProduct $vendorProduct
     * @param \Carbon\Carbon|string $deliveredAt The delivery date
     * @return int Number of days remaining (0 if expired)
     */
    public static function getRemainingRefundDays(?VendorProduct $vendorProduct, $deliveredAt): int
    {
        if (!$vendorProduct || !$deliveredAt) {
            return 0;
        }

        $deadline = self::getRefundDeadline($vendorProduct, $deliveredAt);
        $remainingDays = now()->diffInDays($deadline, false);
        
        return max(0, (int) $remainingDays);
    }

    /**
     * Get vendor delivery date from stage history or fallback to stage updated_at
     * 
     * @param int $orderId
     * @param int $vendorId
     * @return string|null
     */
    public static function getVendorDeliveryDate(int $orderId, int $vendorId): ?string
    {
        $vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::with(['stage', 'history' => function($q) {
            $q->with('newStage')->whereHas('newStage', function($sq) {
                $sq->where('type', 'deliver');
            })->orderBy('created_at', 'desc');
        }])
            ->where('order_id', $orderId)
            ->where('vendor_id', $vendorId)
            ->first();

        if (!$vendorOrderStage) {
            return null;
        }

        // Priority 1: Check stage history for delivered stage
        if ($vendorOrderStage->relationLoaded('history') && $vendorOrderStage->history->isNotEmpty()) {
            $deliveryHistory = $vendorOrderStage->history->first();
            if ($deliveryHistory) {
                return $deliveryHistory->created_at->toDateTimeString();
            }
        }

        // Priority 2: Fallback to stage updated_at if current stage is delivered
        if ($vendorOrderStage->stage && $vendorOrderStage->stage->type === 'deliver') {
            return $vendorOrderStage->updated_at?->toDateTimeString();
        }

        return null;
    }
}
