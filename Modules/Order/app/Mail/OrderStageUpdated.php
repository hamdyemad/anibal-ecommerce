<?php

namespace Modules\Order\app\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;

class OrderStageUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $newStage;

    public function __construct(Order $order, OrderStage $newStage)
    {
        $this->order = $order;
        $this->newStage = $newStage;
    }

    public function build()
    {
        return $this->subject(__('order.stage_updated_subject', ['order_number' => $this->order->order_number]))
                    ->view('order::emails.stage-updated');
    }
}
