<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()),
            'value' => $this->value,
            'type' => $this->type,
            'color' => $this->type === 'color' ? $this->value : null,
            'key_id' => $this->key_id,
            'parent_id' => $this->parent_id,
            'has_children' => $this->whenLoaded('children', fn() => $this->children->count() > 0, false),
            'children_count' => $this->whenLoaded('children', fn() => $this->children->count(), 0),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'key' => $this->when($this->relationLoaded('key') && $this->key, [
                'id' => $this->key?->id,
                'name' => $this->key?->getTranslation('name', app()->getLocale()),
            ]),
            'parent' => $this->when($this->relationLoaded('parent_data') && $this->parent_data, [
                'id' => $this->parent_data?->id,
                'name' => $this->parent_data?->getTranslation('name', app()->getLocale()),
            ]),
        ];
    }
}
