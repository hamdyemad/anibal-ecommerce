<?php

namespace Modules\SystemSetting\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Mobile version only needs text_en, text_ar, and video_link
     */
    public function toArray(Request $request): array
    {
        return [
            'text_en' => $this->getTranslation('section_1_text', 'en') ?? '',
            'text_ar' => $this->getTranslation('section_1_text', 'ar') ?? '',
            'video_link' => $this->section_2_video_link ?? '',
        ];
    }
}
