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
        // Get position data from relationship
        $positionLabel = $this->adPosition ? $this->adPosition->position : null;
        
        // Get width/height from adPosition relationship
        $width = $this->adPosition ? $this->adPosition->width : null;
        $height = $this->adPosition ? $this->adPosition->height : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'image' => ($this->image) ? asset('storage/' . $this->image) : '',
            'link' => $this->link,
            'type' => $this->type,
            'position' => $positionLabel,
            'width' => $width,
            'height' => $height,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
