<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantTreeApiResource extends JsonResource
{
    protected array $selectedPath = [];

    /**
     * Set the selected path for highlighting
     */
    public function setSelectedPath(array $path): self
    {
        $this->selectedPath = $path;
        return $this;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $isSelected = in_array($this->id, $this->selectedPath);
        $hasChildren = $this->relationLoaded('children') && $this->children->count() > 0;

        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()),
            'value' => $this->value,
            'type' => $this->type,
            'color' => $this->type === 'color' ? $this->value : null,
            'key_id' => $this->key_id,
            'parent_id' => $this->parent_id,
            'is_selected' => $isSelected,
            'has_children' => $hasChildren,
            'children_count' => $hasChildren ? $this->children->count() : 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'children' => $isSelected && $hasChildren
                ? $this->children->map(function ($child) {
                    return (new VariantTreeApiResource($child))->setSelectedPath($this->selectedPath)->toArray(request());
                })->toArray()
                : [],
        ];
    }
}
