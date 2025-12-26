<?php

namespace Modules\Order\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;

class OrderStageChanged
{
    use Dispatchable, SerializesModels;

    public $order;
    public $newStage;
    public $previousStage;

    public function __construct(Order $order, OrderStage $newStage, OrderStage $previousStage = null)
    {
        $this->order = $order;
        $this->newStage = $newStage;
        $this->previousStage = $previousStage;
    }
}
