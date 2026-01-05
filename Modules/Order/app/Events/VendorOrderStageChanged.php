<?php

namespace Modules\Order\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;
use Modules\Order\app\Models\VendorOrderStage;

class VendorOrderStageChanged
{
    use Dispatchable, SerializesModels;

    public $order;
    public $vendorId;
    public $newStage;
    public $previousStage;
    public $vendorOrderStage;

    public function __construct(
        Order $order,
        int $vendorId,
        OrderStage $newStage,
        ?OrderStage $previousStage = null,
        ?VendorOrderStage $vendorOrderStage = null
    ) {
        $this->order = $order;
        $this->vendorId = $vendorId;
        $this->newStage = $newStage;
        $this->previousStage = $previousStage;
        $this->vendorOrderStage = $vendorOrderStage;
    }
}
