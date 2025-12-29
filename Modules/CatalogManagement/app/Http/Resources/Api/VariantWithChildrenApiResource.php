<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantWithChildrenApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $hasChildren = $this->relationLoaded('children') && $this->children->count() > 0;

        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()),
            'value' => $this->value,
            'type' => $this->type,
            'color' => $this->type === 'color' ? $this->value : null,
            'key_id' => $this->key_id,
            'parent_id' => $this->parent_id,
            'has_children' => $hasChildren,
            'children_count' => $hasChildren ? $this->children->count() : 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'children' => $hasChildren
                ? VariantWithChildrenApiResource::collection($this->children)
                : [],
        ];
    }
}
