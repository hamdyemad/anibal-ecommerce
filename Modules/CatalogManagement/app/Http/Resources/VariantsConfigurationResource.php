<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantsConfigurationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $data = [
            'id' => $this->id,
            'name' => $this->getTranslation('name', $locale),
        ];

        // Add children recursively if loaded
        if ($this->relationLoaded('childrenRecursive')) {
            $data['children'] = VariantsConfigurationResource::collection($this->childrenRecursive);
        }

        // Add parent if loaded
        if ($this->relationLoaded('parent_data')) {
            $data['parent'] = VariantsConfigurationResource::make($this->parent_data);
        }

        // Add key if loaded
        if ($this->relationLoaded('key')) {
            $data['key'] = VariantsConfigurationKeyResource::make($this->key);
        }

        return $data;
    }
}
