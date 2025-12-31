<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminNotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'icon' => $this->icon,
            'color' => $this->color,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'data' => $this->data,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
        ];
    }
}
