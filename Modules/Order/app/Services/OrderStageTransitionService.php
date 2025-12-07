<?php

namespace Modules\Order\app\Services;

use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;

class OrderStageTransitionService
{
    /**
     * Get allowed transitions for current stage
     */
    public function getAllowedTransitions(Order $order): array
    {
        if (!$order->stage) {
            return [];
        }

        $currentStageSlug = $order->stage->slug;
        $transitions = config('order_stage_transitions.transitions', []);

        // Get allowed stage slugs
        $allowedSlugs = $transitions[$currentStageSlug] ?? [];

        if (empty($allowedSlugs)) {
            return [];
        }

        // Get the actual stage models
        $allowedStages = OrderStage::active()
            ->whereIn('slug', $allowedSlugs)
            ->orderBy('sort_order')
            ->get();

        return $allowedStages->toArray();
    }

    /**
     * Get allowed transitions as collection
     */
    public function getAllowedTransitionsCollection(Order $order)
    {
        if (!$order->stage) {
            return collect([]);
        }

        $currentStageSlug = $order->stage->slug;
        $transitions = config('order_stage_transitions.transitions', []);

        // Get allowed stage slugs
        $allowedSlugs = $transitions[$currentStageSlug] ?? [];

        if (empty($allowedSlugs)) {
            return collect([]);
        }

        // Get the actual stage models
        return OrderStage::active()
            ->whereIn('slug', $allowedSlugs)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Check if transition is allowed
     */
    public function canTransition(Order $order, OrderStage $targetStage): bool
    {
        if (!$order->stage) {
            return false;
        }

        $currentStageSlug = $order->stage->slug;
        $targetStageSlug = $targetStage->slug;
        $transitions = config('order_stage_transitions.transitions', []);

        $allowedSlugs = $transitions[$currentStageSlug] ?? [];

        return in_array($targetStageSlug, $allowedSlugs);
    }

    /**
     * Check if stage requires fulfillment
     */
    public function requiresFulfillment(OrderStage $stage): bool
    {
        $fulfillmentStages = config('order_stage_transitions.fulfillment_stages', []);
        return in_array($stage->slug, $fulfillmentStages);
    }

    /**
     * Check if stage is terminal (no further transitions)
     */
    public function isTerminalStage(OrderStage $stage): bool
    {
        $terminalStages = config('order_stage_transitions.terminal_stages', []);
        return in_array($stage->slug, $terminalStages);
    }

    /**
     * Get fulfillment page URL if required
     */
    public function getFulfillmentPageUrl(Order $order, OrderStage $targetStage): ?string
    {
        if ($this->requiresFulfillment($targetStage)) {
            return route('admin.order-fulfillments.create', ['order' => $order->id]);
        }

        return null;
    }

    /**
     * Get transition message
     */
    public function getTransitionMessage(OrderStage $fromStage, OrderStage $toStage): string
    {
        return trans('order::order.stage_changed_from_to', [
            'from' => $fromStage->name ?? $fromStage->slug,
            'to' => $toStage->name ?? $toStage->slug,
        ]);
    }
}
