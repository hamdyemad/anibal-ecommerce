<?php

namespace Modules\Accounting\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Order\app\Events\OrderStageChanged;
use Modules\Accounting\app\Listeners\ProcessAccountingEntry;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderStageChanged::class => [
            ProcessAccountingEntry::class,
        ],
    ];

    protected static $shouldDiscoverEvents = false;

    protected function configureEmailVerification(): void 
    {
        //
    }
}
