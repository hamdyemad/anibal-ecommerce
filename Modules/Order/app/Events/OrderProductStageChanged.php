<?php

namespace Modules\Order\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderStage;

class OrderProductStageChanged
{
    use Dispatchable, SerializesModels;

    public OrderProduct $orderProduct;
    public OrderStage $newStage;
    public ?OrderStage $previousStage;

    /**
     * Create a new event instance.
     */
    public function __construct(OrderProduct $orderProduct, OrderStage $newStage, ?OrderStage $previousStage = null)
    {
        $this->orderProduct = $orderProduct;
        $this->newStage = $newStage;
        $this->previousStage = $previousStage;
    }
}
