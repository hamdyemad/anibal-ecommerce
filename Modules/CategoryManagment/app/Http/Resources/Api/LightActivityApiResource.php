<?php

namespace Modules\CategoryManagment\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LightActivityApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use fallback logic for activity name
        $activityName = $this->name ?: 'Activity #' . $this->id;

        return [
            'id' => $this->id,
            'name' => $activityName,
            'slug' => $this->slug,
        ];
    }
}
