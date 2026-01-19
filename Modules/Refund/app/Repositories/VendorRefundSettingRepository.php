<?php

namespace Modules\Refund\app\Repositories;

use Modules\Refund\app\Models\VendorRefundSetting;

class VendorRefundSettingRepository
{
    /**
     * Update refund processing days for a vendor
     */
    public function updateRefundDays(int $vendorId, int $days): void
    {
        $vendorSettings = VendorRefundSetting::getForVendor($vendorId);
        $vendorSettings->update(['refund_processing_days' => $days]);
    }

    /**
     * Update customer pays return shipping for a vendor
     */
    public function updateCustomerPaysShipping(int $vendorId, bool $customerPays): void
    {
        $vendorSettings = VendorRefundSetting::getForVendor($vendorId);
        $vendorSettings->update(['customer_pays_return_shipping' => $customerPays]);
    }
}
