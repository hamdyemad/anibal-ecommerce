<?php

namespace Modules\Vendor\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LightVendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'logo' => $this->formatImage($this->logo),
            'active' => (bool) $this->active,
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

        return url(asset('storage/' . $imagePath->path));
    }
}
