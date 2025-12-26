<?php

namespace Modules\Order\app\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\Order\app\Events\OrderStageChanged;
use Modules\Order\app\Mail\OrderStageUpdated;

class SendOrderStageNotification
{
    public function handle(OrderStageChanged $event)
    {
        if ($event->order->customer_email) {
            Mail::to($event->order->customer_email)
                ->send(new OrderStageUpdated($event->order, $event->newStage));
        }
    }
}
