<?php

namespace Modules\SystemSetting\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdsResource extends JsonResource
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
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'image' => ($this->image) ? asset('storage/' . $this->image) : '',
            'link' => $this->link,
            'type' => $this->type,
            'position' => $this->position,
            'mobile_width' => $this->mobile_width,
            'mobile_height' => $this->mobile_height,
            'website_width' => $this->website_width,
            'website_height' => $this->website_height,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
