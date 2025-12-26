<?php

namespace Modules\Accounting\app\Listeners;

use Modules\Accounting\app\Services\AccountingService;
use Modules\Order\app\Events\OrderStageChanged;

class ProcessAccountingEntry
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function handle(OrderStageChanged $event)
    {
        $this->accountingService->processOrderStageChange($event->order, $event->newStage);
    }
}
