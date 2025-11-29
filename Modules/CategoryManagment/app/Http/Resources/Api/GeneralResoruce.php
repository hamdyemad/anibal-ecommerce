<?php

namespace Modules\CategoryManagment\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralResoruce extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'slug' => $this->slug,
            'image' => $this->formatImage($this->image),
            'icon' => $this->formatImage($this->icon),
            'type' => $this->type ?? 'General',
        ];
    }

    /**
     * Format image path to full URL
     */
    private function formatImage($imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }

        return url(asset('storage/' . $imagePath));
    }
}
