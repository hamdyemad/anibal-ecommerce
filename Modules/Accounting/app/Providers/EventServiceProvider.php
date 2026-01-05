<?php

namespace Modules\Accounting\app\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Order\app\Events\OrderStageChanged;
use Modules\Order\app\Events\VendorOrderStageChanged;
use Modules\Accounting\app\Listeners\ProcessAccountingEntry;
use Modules\Accounting\app\Listeners\ProcessVendorAccountingEntry;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderStageChanged::class => [
            ProcessAccountingEntry::class,
        ],
        VendorOrderStageChanged::class => [
            ProcessVendorAccountingEntry::class,
        ],
    ];

    protected static $shouldDiscoverEvents = false;

    protected function configureEmailVerification(): void 
    {
        //
    }
}
