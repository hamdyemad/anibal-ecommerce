<?php

namespace Modules\SystemSetting\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Customer\app\Transformers\CustomerApiResource;

class BlogCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'customer' => new CustomerApiResource($this->customer),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
