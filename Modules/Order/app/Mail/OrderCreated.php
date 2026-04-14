<?php

namespace Modules\Order\app\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Order\app\Models\Order;

class OrderCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $locale = app()->getLocale();
        
        return $this->subject(trans('order::order.new_order_email_subject', ['order_number' => $this->order->order_number]))
                    ->view('order::emails.order-created')
                    ->with([
                        'order' => $this->order,
                        'locale' => $locale,
                    ]);
    }
}
