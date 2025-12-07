<?php

namespace Modules\Order\app\Repositories\Api;

use Modules\Order\app\Interfaces\Api\OrderStageApiRepositoryInterface;
use Modules\Order\app\Models\OrderStage;
use Modules\Order\app\Models\Order;

class OrderStageApiRepository implements OrderStageApiRepositoryInterface
{
    /**
     * Get all active order stages
     */
    public function getActiveOrderStages()
    {
        return OrderStage::active()
            ->with(['translations'])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get order stage by ID
     */
    public function getOrderStageById($id)
    {
        return OrderStage::with(['translations'])
            ->findOrFail($id);
    }

    /**
     * Get allowed transitions for an order
     * Filters stages based on current order stage
     */
    public function getAllowedStagesForOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        if (!$order->stage) {
            return collect([]);
        }

        $currentStageSlug = $order->stage->slug;
        $transitions = config('order_stage_transitions.transitions', []);

        // Get allowed stage slugs from config
        $allowedSlugs = $transitions[$currentStageSlug] ?? [];

        if (empty($allowedSlugs)) {
            return collect([]);
        }

        // Get the actual stage models
        return OrderStage::active()
            ->whereIn('slug', $allowedSlugs)
            ->with(['translations'])
            ->orderBy('sort_order')
            ->get();
    }
}
