<?php

namespace Modules\Order\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingResource extends JsonResource
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
            'index' => $this->index ?? null,
            'name' => $this->getTranslation('name', app()->getLocale()),
            'name_en' => $this->getTranslation('name', "en"),
            'name_ar' => $this->getTranslation('name', "ar"),
            'cost' => $this->cost,
            'active' => $this->active,
            'city' => [
                'id' => $this->city?->id,
                'name' => $this->city?->name,
            ],
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'country' => [
                'id' => $this->country?->id,
                'name' => $this->country?->name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
