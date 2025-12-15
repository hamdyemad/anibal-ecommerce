<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OccasionResource extends JsonResource
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
            'vendor_id' => $this->vendor_id,
            'vendor' => [
                'id' => $this->vendor?->id,
                'name' => $this->vendor?->name,
                'slug' => $this->vendor?->slug,
            ],
            'name' => $this->name ?? '',
            'title' => $this->getTranslation('title', app()->getLocale()) ?? '',
            'sub_title' => $this->getTranslation('sub_title', app()->getLocale()) ?? '',
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'is_active' => $this->is_active,
            'slug' => $this->slug,

            // Image
            'image' => $this->attachments()
                ->where('type', 'image')
                ->first()?->path ? asset('storage/' . $this->attachments()->where('type', 'image')->first()->path) : null,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
