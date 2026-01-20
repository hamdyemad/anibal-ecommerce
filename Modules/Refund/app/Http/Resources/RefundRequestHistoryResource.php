<?php

namespace Modules\Refund\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundRequestHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'refund_request_id' => $this->refund_request_id,
            'old_status' => $this->old_status,
            'old_status_label' => $this->old_status ? trans('refund::refund.statuses.' . $this->old_status) : null,
            'new_status' => $this->new_status,
            'new_status_label' => trans('refund::refund.statuses.' . $this->new_status),
            'user_id' => $this->user_id,
            'user_name' => $this->user?->name,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
