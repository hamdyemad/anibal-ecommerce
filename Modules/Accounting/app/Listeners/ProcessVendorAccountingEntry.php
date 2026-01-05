<?php

namespace Modules\Accounting\app\Listeners;

use Modules\Accounting\app\Services\AccountingService;
use Modules\Order\app\Events\VendorOrderStageChanged;

class ProcessVendorAccountingEntry
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function handle(VendorOrderStageChanged $event)
    {
        $this->accountingService->processVendorOrderStageChange(
            $event->order,
            $event->vendorId,
            $event->newStage,
            $event->previousStage
        );
    }
}
