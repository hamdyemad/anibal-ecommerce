<?php

namespace Modules\Refund\app\Services;

use Modules\Refund\app\Repositories\VendorRefundSettingRepository;

class VendorRefundSettingService
{
    public function __construct(
        protected VendorRefundSettingRepository $vendorRefundSettingRepository
    ) {
    }

    /**
     * Update refund processing days for a vendor
     */
    public function updateRefundDays(int $vendorId, int $days): void
    {
        $this->vendorRefundSettingRepository->updateRefundDays($vendorId, $days);
    }

    /**
     * Update customer pays return shipping for a vendor
     */
    public function updateCustomerPaysShipping(int $vendorId, bool $customerPays): void
    {
        $this->vendorRefundSettingRepository->updateCustomerPaysShipping($vendorId, $customerPays);
    }
}
