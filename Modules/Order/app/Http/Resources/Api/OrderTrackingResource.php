<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $createdAt = null;
        try {
            if ($this->created_at && method_exists($this->created_at, 'format')) {
                $createdAt = $this->created_at->format('Y-m-d H:i:s');
            }
        } catch (\Exception $e) {
            $createdAt = null;
        }

        $currentStage = null;
        if ($this->stage) {
            $currentStage = [
                'id' => $this->stage->id ?? 0,
                'name' => $this->stage->name ?? 'N/A',
                'color' => $this->stage->color ?? '#000000',
            ];
        }

        $vendorsTracking = [];
        if ($this->vendorStages && $this->vendorStages->count() > 0) {
            $vendorsTracking = $this->vendorStages->map(function ($vendorStage) {
                $stageHistory = [];
                if ($vendorStage->history && $vendorStage->history->count() > 0) {
                    $stageHistory = $vendorStage->history->map(function ($history) {
                        $changedAt = null;
                        try {
                            if ($history->created_at && method_exists($history->created_at, 'format')) {
                                $changedAt = $history->created_at->format('Y-m-d H:i:s');
                            }
                        } catch (\Exception $e) {
                            $changedAt = null;
                        }

                        return [
                            'id' => $history->id ?? 0,
                            'old_stage' => $history->oldStage ? [
                                'id' => $history->oldStage->id ?? 0,
                                'name' => $history->oldStage->name ?? 'N/A',
                            ] : null,
                            'new_stage' => $history->newStage ? [
                                'id' => $history->newStage->id ?? 0,
                                'name' => $history->newStage->name ?? 'N/A',
                            ] : null,
                            'notes' => $history->notes ?? '',
                            'changed_by' => $history->user?->name ?? 'System',
                            'changed_at' => $changedAt,
                        ];
                    })->toArray();
                }

                return [
                    'vendor' => [
                        'id' => $vendorStage->vendor_id ?? 0,
                        'name' => $vendorStage->vendor?->name ?? 'N/A',
                    ],
                    'current_stage' => [
                        'id' => $vendorStage->stage?->id ?? 0,
                        'name' => $vendorStage->stage?->name ?? 'N/A',
                        'color' => $vendorStage->stage?->color ?? '#000000',
                    ],
                    'stage_history' => $stageHistory,
                ];
            })->toArray();
        }

        return [
            'id' => $this->id ?? 0,
            'order_number' => $this->order_number ?? '',
            'created_at' => $createdAt,
            'total' => (float) ($this->total_price ?? 0),
            'current_stage' => $currentStage,
            'vendors_tracking' => $vendorsTracking,
        ];
    }
}
