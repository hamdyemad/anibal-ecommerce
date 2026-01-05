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
        // Determine which width/height to return based on requested type
        $requestedType = $request->input('type');
        
        // If type is specified, return dimensions for that type
        // Otherwise return based on the ad's type array (prefer website if both)
        if ($requestedType === 'mobile') {
            $width = $this->mobile_width;
            $height = $this->mobile_height;
        } elseif ($requestedType === 'website') {
            $width = $this->website_width;
            $height = $this->website_height;
        } else {
            // Default: if ad supports website, use website dimensions, otherwise mobile
            if (is_array($this->type) && in_array('website', $this->type)) {
                $width = $this->website_width;
                $height = $this->website_height;
            } else {
                $width = $this->mobile_width;
                $height = $this->mobile_height;
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'image' => ($this->image) ? asset('storage/' . $this->image) : '',
            'link' => $this->link,
            'type' => $this->type,
            'position' => $this->position,
            'width' => $width,
            'height' => $height,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
